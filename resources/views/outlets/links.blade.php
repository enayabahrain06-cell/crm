<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $outlet->name }} - Connect With Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; }
        .hero-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 3rem 1rem; text-align: center; color: white; }
        .outlet-logo { width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 3rem; }
        .links-section { flex: 1; padding: 2rem 1rem; background: #f8f9fa; }
        .social-link { display: flex; align-items: center; justify-content: center; padding: 1rem; margin-bottom: 1rem; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.1rem; }
        .social-link:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .social-link i { font-size: 1.5rem; margin-right: 0.75rem; }
        .social-link.instagram { background: linear-gradient(45deg, #f09433, #dc2743, #bc1888); color: white; }
        .social-link.facebook { background: #1877f2; color: white; }
        .social-link.whatsapp { background: #25d366; color: white; }
        .social-link.website { background: #4f46e5; color: white; }
        .social-link.tiktok { background: #000; color: white; }
        .social-link.other { background: #64748b; color: white; }
        .footer { text-align: center; padding: 1.5rem; color: #64748b; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="outlet-logo"><i class="bi bi-shop"></i></div>
        <h2>{{ $outlet->name }}</h2>
        @if($outlet->description)<p class="mb-0">{{ $outlet->description }}</p>@endif
    </div>
    <div class="links-section">
        <div class="container" style="max-width: 480px;">
            @forelse($links as $link)
            <a href="{{ $link->url }}" target="_blank" class="social-link {{ $link->platform }}">
                <i class="bi bi-{{ $link->platform }}"></i>{{ $link->label }}
            </a>
            @empty
            <div class="text-center text-muted py-5"><i class="bi bi-link display-4"></i><p class="mt-2">No links available</p></div>
            @endforelse
        </div>
    <div class="footer"><p class="mb-0">{{ $outlet->name }}</p>@if($outlet->address)<small>{{ $outlet->address }}</small>@endif</div>
</body>
</html>
