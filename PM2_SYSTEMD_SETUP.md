# Auto-Start Setup for Laravel Artisan Serve

## Systemd Service Configuration

A systemd service has been created to automatically run the Laravel development server on VM startup.

### Service Details
- **Service Name**: `laravel-serve`
- **Service File**: `/etc/systemd/system/laravel-serve.service`
- **Listening Port**: `8000` (accessible on `0.0.0.0:8000`)
- **Working Directory**: `/var/www/hospitality-crm`

### Management Commands

```bash
# Check service status
systemctl status laravel-serve.service

# Stop the service
systemctl stop laravel-serve.service

# Start the service
systemctl start laravel-serve.service

# Restart the service
systemctl restart laravel-serve.service

# Disable auto-start on boot
systemctl disable laravel-serve.service

# Re-enable auto-start on boot
systemctl enable laravel-serve.service

# View logs
journalctl -u laravel-serve.service -f

# View logs from specific time
journalctl -u laravel-serve.service --since "2025-01-01"
```

### Logs
- **Standard Output**: `/var/log/laravel-serve.log`
- **Error Output**: `/var/log/laravel-serve-error.log`

### Features
✅ Automatically starts on VM boot/reboot  
✅ Automatically restarts if process crashes (Restart=always)  
✅ 5-second delay before restart to prevent rapid restart loops  
✅ Logs captured to files for troubleshooting  
✅ Accessible on all network interfaces (`0.0.0.0:8000`)

### Access the Application
- Local access: http://localhost:8000
- Network access: http://<VM-IP>:8000

### Current Status
Service is **enabled** and **active (running)**

### Service Configuration
```ini
[Unit]
Description=Laravel Artisan Development Server
After=network.target mysql.service postgresql.service

[Service]
Type=simple
User=root
WorkingDirectory=/var/www/hospitality-crm
ExecStart=/usr/bin/php artisan serve --host=0.0.0.0 --port=8000
Restart=always
RestartSec=5
StandardOutput=append:/var/log/laravel-serve.log
StandardError=append:/var/log/laravel-serve-error.log

[Install]
WantedBy=multi-user.target
```

