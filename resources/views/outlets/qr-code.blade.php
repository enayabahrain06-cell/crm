@extends('layouts.app')

@section('title', 'QR Code')
@section('page-title', 'QR Code - ' . $outlet->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">QR Code</h5>
                    <small class="text-muted">{{ $outlet->name }}</small>
                </div>
                <a href="{{ route('outlets.show', $outlet) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="bg-white p-4 rounded d-inline-block border">
                        <div id="qrcode"></div>
                    </div>
                </div>
                
                <h6 class="mb-3">{{ $outlet->name }}</h6>
                
                <div class="mb-4">
                    <code class="bg-light px-3 py-2 rounded">{{ $outlet->code }}</code>
                </div>

                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Customers can scan this QR code to register with your outlet.
                </div>

                <div class="input-group mb-4">
                    <input type="text" class="form-control" value="{{ $registrationUrl }}" readonly id="qrUrl">
                    <button class="btn btn-outline-primary" type="button" onclick="copyUrl()">
                        <i class="bi bi-clipboard"></i> Copy URL
                    </button>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ $registrationUrl }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Open URL
                    </a>
                    <button class="btn btn-primary" onclick="downloadQr()">
                        <i class="bi bi-download me-1"></i>Download QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code
    const qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "{{ $registrationUrl }}",
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    function copyUrl() {
        const urlInput = document.getElementById('qrUrl');
        urlInput.select();
        navigator.clipboard.writeText(urlInput.value).then(() => {
            alert('URL copied to clipboard!');
        });
    }

    function downloadQr() {
        const canvas = document.querySelector('#qrcode canvas');
        if (canvas) {
            const link = document.createElement('a');
            link.download = 'qrcode-{{ $outlet->code }}.png';
            link.href = canvas.toDataURL();
            link.click();
        } else {
            // Fallback if using an image
            const img = document.querySelector('#qrcode img');
            if (img) {
                const link = document.createElement('a');
                link.download = 'qrcode-{{ $outlet->code }}.png';
                link.href = img.src;
                link.click();
            }
        }
    }
</script>
@endpush
@endsection

