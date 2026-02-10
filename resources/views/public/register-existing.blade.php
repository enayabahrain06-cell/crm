<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Already Registered - {{ $outlet->name }}</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <h3>Welcome Back!</h3>
                        <p class="mb-0">You're already registered with {{ $outlet->name }}</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h5 class="mb-3">Hi, {{ $customer->name }}!</h5>
                            <p class="text-muted">
                                We found your account in our system. You're already a member of our loyalty program.
                            </p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="bi bi-star-fill text-warning fs-4"></i>
                                    <div class="mt-2">
                                        <strong>{{ number_format($customer->loyaltyWallet->points_balance ?? 0) }}</strong>
                                        <small class="text-muted d-block">Points</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="bi bi-award-fill text-primary fs-4"></i>
                                    <div class="mt-2">
                                        <strong>{{ $customer->loyaltyWallet->loyaltyTier->name ?? 'Bronze' }}</strong>
                                        <small class="text-muted d-block">Tier</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary-custom text-white">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                            <a href="{{ url('/register?outlet=' . $outlet->code) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat me-2"></i>Register Another Account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

