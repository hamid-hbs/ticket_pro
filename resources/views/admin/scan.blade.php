@extends('layouts.admin', ['title' => 'Scanner'])

@section('admin_content')
    <h1>Validation d’entrée</h1>
    <p style="color:var(--muted); max-width:520px;">Saisissez le texte du QR code ou utilisez l’icône caméra pour prendre une image du code. Dans les deux cas, le billet valide et payé sera marqué comme utilisé.</p>

    @if (session('scan_result'))
        @php $r = session('scan_result'); @endphp
        @if (!empty($r['ok']))
            <div class="flash-ok">{{ $r['message'] }}</div>
            @if (!empty($r['ticket']))
                <div class="card" style="margin-bottom:1rem;">
                    <strong>{{ $r['ticket']->name }}</strong> — {{ $r['ticket']->email }}
                </div>
            @endif
        @else
            <div class="flash-err">{{ $r['message'] }}</div>
            @if (!empty($r['ticket']))
                <div class="card" style="margin-bottom:1rem; font-size:0.9rem;">
                    <div>{{ $r['ticket']->name }} — {{ $r['ticket']->email }}</div>
                    <div>
                        Statut actuel : <strong>
                            @if($r['ticket']->status === 'paid')
                                Payé
                            @elseif($r['ticket']->status === 'used')
                                Utilisé
                            @else
                                Non payé
                            @endif
                        </strong>
                    </div>
                </div>
            @endif
        @endif
    @endif

    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('admin.scan.process') }}" id="scan-form">
            @csrf
            <label for="qr_code">Contenu du QR code</label>
            <div style="display:flex; gap:0.75rem; align-items:center;">
                <input type="text" id="qr_code" name="qr_code" value="{{ old('qr_code') }}" required autofocus placeholder="Collez ou scannez ici" autocomplete="off" style="flex:1;">
                <button type="button" id="camera-btn" aria-label="Ouvrir la caméra" title="Scanner avec la caméra" style="width:48px; height:48px; display:inline-flex; align-items:center; justify-content:center; padding:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </button>
            </div>
            <input type="file" id="qr_image" accept="image/*" capture="environment" hidden>
            <div id="qr_status" style="margin-top:0.75rem;color:var(--muted);font-size:0.95rem;"></div>
            @error('qr_code')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="margin-top:1rem;">
                <button type="submit">Valider l’entrée</button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/jsqr/dist/jsQR.js"></script>
    <script>
        const cameraButton = document.getElementById('camera-btn');
        const imageInput = document.getElementById('qr_image');
        const qrCodeField = document.getElementById('qr_code');
        const qrStatus = document.getElementById('qr_status');
        const scanForm = document.getElementById('scan-form');

        cameraButton.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', () => {
            const file = imageInput.files && imageInput.files[0];

            if (!file) {
                qrStatus.textContent = '';
                return;
            }

            qrStatus.textContent = 'Lecture de l’image en cours...';

            const reader = new FileReader();

            reader.onload = () => {
                const image = new Image();

                image.onload = () => {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    canvas.width = image.width;
                    canvas.height = image.height;
                    context.drawImage(image, 0, 0);

                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const result = jsQR(imageData.data, canvas.width, canvas.height);

                    if (result && result.data) {
                        qrCodeField.value = result.data;
                        qrStatus.textContent = 'QR détecté. Validation automatique en cours...';
                        scanForm.submit();
                        return;
                    }

                    qrStatus.textContent = 'Aucun QR code lisible trouvé dans cette image.';
                    qrCodeField.focus();
                };

                image.onerror = () => {
                    qrStatus.textContent = 'Impossible de lire cette image.';
                };

                image.src = reader.result;
            };

            reader.onerror = () => {
                qrStatus.textContent = 'Erreur lors de la lecture du fichier.';
            };

            reader.readAsDataURL(file);
        });
    </script>
@endsection
