<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    protected $backupPath = 'backups';

    public function __construct()
    {
        $this->middleware(['role:super_admin']);
    }

    /**
     * Display backup management page
     */
    public function index(Request $request)
    {
        $backups = $this->getBackupList();
        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Create a new backup
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'full');

        $filename = 'backup_' . $type . '_' . date('Y-m-d_His') . '.zip';

        try {
            $zip = new ZipArchive();
            if ($zip->open(storage_path('app/' . $this->backupPath . '/' . $filename), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

                if (in_array($type, ['database', 'full'])) {
                    // Create database dump
                    $databaseDump = $this->createDatabaseDump();
                    $zip->addFromString('database.sql', $databaseDump);
                }

                if (in_array($type, ['files', 'full'])) {
                    // Add storage files
                    $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage');
                }

                if ($type === 'full') {
                    // Add .env file
                    if (file_exists(base_path('.env'))) {
                        $zip->addFile(base_path('.env'), 'config/.env');
                    }
                }

                $zip->close();
            }

            // Log the backup creation
            Log::channel('activity')->info('Backup created', [
                'user_id' => Auth::id(),
                'type' => $type,
                'filename' => $filename
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Backup created successfully: ' . $filename);

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download(Request $request, $filename)
    {
        $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Backup file not found');
        }

        return response()->download($filepath, $filename);
    }

    /**
     * Restore from a backup
     */
    public function restore(Request $request, $filename)
    {
        $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Backup file not found');
        }

        try {
            $zip = new ZipArchive();
            $zip->open($filepath);

            // Extract database.sql if exists
            if ($zip->locateName('database.sql') !== false) {
                $sqlContent = $zip->getFromName('database.sql');

                // Only disable foreign key checks for MySQL/MariaDB (not supported in SQLite)
                $driver = DB::connection()->getDriverName();
                $isMySQL = in_array($driver, ['mysql', 'mariadb']);

                if ($isMySQL) {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                }

                // For SQLite, drop existing tables first to avoid "table already exists" errors
                if ($driver === 'sqlite') {
                    $tablesToSkip = ['sessions']; // Skip sessions to avoid breaking active session during restore
                    $existingTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'laravel_%'");
                    foreach ($existingTables as $tableObj) {
                        $tableName = $tableObj->name;
                        if (!in_array($tableName, $tablesToSkip)) {
                            DB::statement("DROP TABLE IF EXISTS \"{$tableName}\"");
                        }
                    }
                }

                // Execute SQL statements
                foreach (array_filter(array_map('trim', explode(';', $sqlContent))) as $statement) {
                    if (!empty($statement)) {
                        DB::statement($statement);
                    }
                }

                if ($isMySQL) {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                }
            }

            $zip->close();

            // Log the restore
            Log::channel('activity')->info('Backup restored', [
                'user_id' => Auth::id(),
                'filename' => $filename
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Backup restored successfully');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Failed to restore backup: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup
     */
    public function delete(Request $request, $filename)
    {
        $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Backup file not found');
        }

        try {
            unlink($filepath);

            // Log the deletion
            Log::channel('activity')->info('Backup deleted', [
                'user_id' => Auth::id(),
                'filename' => $filename
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Backup deleted successfully');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    /**
     * Create database dump
     */
    protected function createDatabaseDump()
    {
        $dump = '';

        // Get database connection type
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite-specific queries
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'laravel_%'");

            foreach ($tables as $tableObj) {
                $tableName = $tableObj->name;

                // Get create table statement for SQLite
                $createResult = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
                $dump .= "\n\n" . ($createResult[0]->sql ?? '') . ";\n\n";

                // Get table data using query builder
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif (is_numeric($value)) {
                            $values[] = $value;
                        } elseif (is_bool($value)) {
                            $values[] = $value ? '1' : '0';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $columns = implode(', ', array_map(function($col) {
                        return '"' . $col . '"';
                    }, array_keys((array)$row)));
                    $dump .= "INSERT INTO \"{$tableName}\" ({$columns}) VALUES (" . implode(', ', $values) . ");\n";
                }
            }
        } else {
            // MySQL/MariaDB queries (original implementation)
            $tables = DB::select('SHOW TABLES');

            foreach ($tables as $table) {
                $tableName = reset($table);

                // Get create table statement
                $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $dump .= "\n\n" . $create[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                    }
                    $dump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
            }
        }

        return $dump;
    }

    /**
     * Add directory to zip recursively
     */
    protected function addDirectoryToZip(ZipArchive $zip, $sourcePath, $zipPath)
    {
        if (!is_dir($sourcePath)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($sourcePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Get list of existing backups
     */
    protected function getBackupList()
    {
        $backups = [];
        $path = storage_path('app/' . $this->backupPath);

        if (!is_dir($path)) {
            return $backups;
        }

        $files = File::files($path);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $filename = $file->getFilename();
                $type = 'full';
                if (strpos($filename, 'database_') !== false) {
                    $type = 'database';
                } elseif (strpos($filename, 'files_') !== false) {
                    $type = 'files';
                }

                $backups[] = [
                    'filename' => $filename,
                    'type' => $type,
                    'size' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        // Sort by date descending
        usort($backups, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

