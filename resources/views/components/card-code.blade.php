@props([
    'barcode' => null,
    'qr' => null,
    'barcodeHeight' => 60,
    'title' => 'كود التعريف',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'card-code-wrapper']) }}>
    <div class="row g-3 g-md-4 justify-content-center align-items-center">

        {{-- Barcode Section --}}
        @if($barcode)
            <div class="col-12 col-md-auto">
                <div class="barcode-section text-center">
                    <div class="barcode-box p-3 bg-white rounded-3 border mx-auto">
                        <div class="barcode-svg-wrapper">
                            {!! DNS1D::getBarcodeHTML($barcode, 'C128', 1.5, $barcodeHeight) !!}
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted d-block">
                            <i class="ph-duotone ph-barcode me-1"></i>
                            {{ $title }}
                        </small>
                    </div>
                </div>
            </div>
        @endif

        {{-- QR Section --}}
        @if($qr)
            <div class="col-12 col-md-auto">
                <div class="qr-section text-center">
                    <div class="qr-box p-2 bg-white rounded-3 border d-inline-block">
                        <div class="qr-svg-wrapper">
                            {!! DNS2D::getBarcodeHTML($qr, 'QRCODE', 3.5, 3.5) !!}
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted d-block">
                            <i class="ph-duotone ph-qr-code me-1"></i>
                            امسح للوصول
                        </small>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @if($subtitle)
        <div class="text-center mt-3">
            <small class="text-muted">{{ $subtitle }}</small>
        </div>
    @endif
</div>
