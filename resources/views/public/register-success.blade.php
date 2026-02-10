<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - {{ $outlet->name }}</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        .welcome-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
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
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h3>Welcome to {{ $outlet->name }}!</h3>
                        <p class="mb-0">Your registration was successful</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="welcome-icon">🎉</div>
                            <h5 class="mb-3">Hi, {{ $customer->name }}!</h5>
                            <p class="text-muted">
                                Congratulations! You're now a member of our loyalty program. 
                                Start earning points on every visit!
                            </p>
                        </div>

                        @if(isset($visit_recorded) && $visit_recorded)
                        <div class="alert alert-success mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Visit Recorded!</strong>
                            <p class="mb-0 small">Your visit to {{ $outlet->name }} has been recorded. Points will be awarded based on your bill amount.</p>
                        </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="bi bi-gift-fill text-success fs-4"></i>
                                    <div class="mt-2">
                                        <strong>Welcome Bonus</strong>
                                        <small class="text-muted d-block">100 Points</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="bi bi-star-fill text-warning fs-4"></i>
                                    <div class="mt-2">
                                        <strong>Current Balance</strong>
                                        <small class="text-muted d-block">{{ number_format($customer->loyaltyWallet->points_balance ?? 100) }} Points</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Next Steps:</strong>
                            <ul class="mb-0 text-start small">
                                <li>Show your membership at checkout to earn points</li>
                                <li>Download our app for exclusive offers</li>
                                <li>Check your email for your membership details</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary-custom text-white">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                            <a href="{{ url('/register?outlet=' . $outlet->code) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-person-plus me-2"></i>Register Another Member
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

