<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); max-width: 400px; width: 100%; }
        .login-header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 2rem; text-align: center; border-radius: 15px 15px 0 0; }
        .login-body { padding: 2rem; }
        .btn-primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h3 class="mb-0"><i class="bi bi-building"></i> Hospitality CRM</h3>
            <p class="mb-0 mt-2">Group Loyalty Platform</p>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
                </div>
            </form>
            <hr>
            <div class="text-center">
                <small class="text-muted">Demo Credentials:</small>
                <div class="small mt-1">
                    <strong>Super Admin:</strong> admin@hospitality.com / password123<br>
                    <strong>Manager:</strong> manager@hospitality.com / password123
                </div>
        </div>
</body>
</html>
