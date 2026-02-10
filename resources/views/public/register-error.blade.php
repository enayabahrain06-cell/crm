<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .result-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 2rem;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            font-weight: 600;
            padding: 12px 24px;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="result-card">
                    <div class="card-header-custom">
                        <div class="icon-circle">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h3>Unable to Register</h3>
                        <p class="mb-0">We encountered an issue</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 mb-3">Registration Failed</h5>
                            <p class="text-muted">
                                {{ $message ?? 'An error occurred during registration. Please try again.' }}
                            </p>
                        </div>

                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Possible reasons:</strong>
                            <ul class="mb-0 text-start small mt-2">
                                <li>The QR code may be invalid or expired</li>
                                <li>The outlet may have been deactivated</li>
                                <li>You may already be registered with this outlet</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ url('/') }}" class="btn btn-primary-custom text-white">
                                <i class="bi bi-house me-2"></i>Go to Homepage
                            </a>
                            <button onclick="window.history.back()" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Go Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

