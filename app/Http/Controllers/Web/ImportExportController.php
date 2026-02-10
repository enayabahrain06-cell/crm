<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ImportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportExportController extends Controller
{
    protected $importExportService;

    /**
     * Custom permission map for non-standard permission names.
     * Override the default plural-based permission names.
     */
    protected $customPermissionMap = [
        'view' => 'import_export.view',
        'import' => 'import_export.import',
        'export' => 'import_export.export',
    ];

    public function __construct(ImportExportService $importExportService)
    {
        $this->importExportService = $importExportService;
    }

    /**
     * Display the import/export page.
     */
    public function index()
    {
        $this->authorize('import_export.view');
        $recentExports = Storage::files('exports');
        return view('import-export.index', compact('recentExports'));
    }

    /**
     * Handle file import.
     */
    public function import(Request $request)
    {
        $this->authorize('import_export.import');

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'type' => 'required|in:customers,visits',
        ]);

        $result = $this->importExportService->import(
            $request->file('file'),
            $request->type
        );

        if ($result['success']) {
            return back()->with('success', "Successfully imported {$result['imported']} records.");
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Handle file export.
     */
    public function export(Request $request)
    {
        $this->authorize('import_export.export');

        $request->validate([
            'type' => 'required|in:customers,visits,loyalty',
            'format' => 'nullable|in:csv,xlsx',
        ]);

        $result = $this->importExportService->export(
            $request->type,
            $request->format ?? 'csv'
        );

        if ($result['success']) {
            return back()->with('success', 'Export started. Check back shortly.');
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Download an export file.
     */
    public function downloadExport($file)
    {
        $this->authorize('import_export.view');
        return Storage::download("exports/{$file}");
    }
}

