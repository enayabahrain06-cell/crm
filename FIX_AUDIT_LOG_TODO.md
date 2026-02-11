# Fix AuditLog and Permission Issues

## Issues Identified:
1. **NOT NULL constraint on `event` column**: The database has `event` column as NOT NULL but `AuditLog::log()` doesn't set it
2. **Permission denied on laravel.log**: Web server can't write to log file

## Tasks Completed:
- [x] 1. Created migration `2024_01_01_000014_fix_audit_logs_event_column.php`
- [x] 2. Updated AuditLog::log() to also set `event` column (added 'event' to fillable and set it from $action)
- [x] 3. Fixed file permissions on storage/logs/laravel.log (chmod 666, chown www-data)
- [x] 4. Fixed existing NULL values in event column

## Files Changed:
1. `database/migrations/2024_01_01_000014_fix_audit_logs_event_column.php` - New migration
2. `app/Models/AuditLog.php` - Added 'event' to fillable array and set it in log() method

