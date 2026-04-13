@extends('layouts.admin', ['title' => 'Scanner'])

@section('admin_content')
    <h1>Validation d’entrée</h1>
    <p style="color:var(--muted); max-width:520px;">Saisissez le texte du QR code ou utilisez l’icône caméra pour lancer/arrêter le scan en direct. Le billet valide et payé sera marqué comme utilisé.</p>

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
                <button type="button" id="camera-btn" aria-label="Démarrer le scan caméra" title="Démarrer le scan caméra" style="width:48px; height:48px; display:inline-flex; align-items:center; justify-content:center; padding:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </button>
            </div>
            <input type="file" id="qr_image" accept="image/*" capture="environment" hidden>
            <div id="live-wrap" style="display:none; margin-top:0.85rem; border:1px solid rgba(15, 23, 42, 0.1); border-radius:12px; overflow:hidden; background:#0f172a;">
                <video id="live-video" playsinline muted style="display:block; width:100%; max-height:360px; object-fit:cover;"></video>
            </div>
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
        const liveWrap = document.getElementById('live-wrap');
        const liveVideo = document.getElementById('live-video');
        const isMobileDevice = /Android|iPhone|iPad|iPod|Windows Phone|Mobile/i.test(navigator.userAgent)
            || (window.matchMedia && window.matchMedia('(pointer: coarse)').matches);

        let liveStream = null;
        let liveScanRunning = false;
        let liveDetector = null;
        let liveFrameRequest = null;
        let lastDecodeAt = 0;

        const setStatus = (message) => {
            qrStatus.textContent = message;
        };

        const setCameraButtonState = (running) => {
            if (!isMobileDevice) {
                cameraButton.classList.remove('danger');
                cameraButton.setAttribute('aria-label', 'Importer une image du QR');
                cameraButton.setAttribute('title', 'Importer une image du QR');
                return;
            }

            if (running) {
                cameraButton.classList.add('danger');
                cameraButton.setAttribute('aria-label', 'Arrêter le scan caméra');
                cameraButton.setAttribute('title', 'Arrêter le scan caméra');
                return;
            }

            cameraButton.classList.remove('danger');
            cameraButton.setAttribute('aria-label', 'Démarrer le scan caméra');
            cameraButton.setAttribute('title', 'Démarrer le scan caméra');
        };

        const stopLiveScan = () => {
            liveScanRunning = false;

            if (liveFrameRequest) {
                cancelAnimationFrame(liveFrameRequest);
                liveFrameRequest = null;
            }

            if (liveStream) {
                liveStream.getTracks().forEach((track) => track.stop());
                liveStream = null;
            }

            liveVideo.srcObject = null;
            liveWrap.style.display = 'none';
            setCameraButtonState(false);
        };

        const submitDecodedValue = (value) => {
            if (!value) {
                return false;
            }

            qrCodeField.value = String(value).trim();
            setStatus('QR détecté. Validation automatique en cours...');
            scanForm.submit();
            return true;
        };

        const decodeWithJsQr = (image) => {
            if (typeof jsQR !== 'function') {
                return null;
            }

            const scales = [1, 0.75, 0.5, 0.35];

            for (const scale of scales) {
                const width = Math.max(300, Math.round(image.width * scale));
                const height = Math.max(300, Math.round(image.height * scale));
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d', { willReadFrequently: true });

                canvas.width = width;
                canvas.height = height;
                context.drawImage(image, 0, 0, width, height);

                const imageData = context.getImageData(0, 0, width, height);
                const direct = jsQR(imageData.data, width, height, { inversionAttempts: 'attemptBoth' });

                if (direct && direct.data) {
                    return direct.data;
                }

                const contrasted = new Uint8ClampedArray(imageData.data);
                for (let i = 0; i < contrasted.length; i += 4) {
                    const r = contrasted[i];
                    const g = contrasted[i + 1];
                    const b = contrasted[i + 2];
                    const luma = 0.299 * r + 0.587 * g + 0.114 * b;
                    const v = luma > 128 ? 255 : 0;
                    contrasted[i] = v;
                    contrasted[i + 1] = v;
                    contrasted[i + 2] = v;
                }

                const fallback = jsQR(contrasted, width, height, { inversionAttempts: 'attemptBoth' });
                if (fallback && fallback.data) {
                    return fallback.data;
                }
            }

            return null;
        };

        const decodeWithBarcodeDetector = async (file) => {
            if (!('BarcodeDetector' in window)) {
                return null;
            }

            try {
                const detector = new BarcodeDetector({ formats: ['qr_code'] });
                const bitmap = await createImageBitmap(file);
                const codes = await detector.detect(bitmap);
                return codes[0]?.rawValue ?? null;
            } catch (error) {
                return null;
            }
        };

        const detectFromLiveFrame = async () => {
            if (!liveScanRunning) {
                return;
            }

            const now = performance.now();
            if (now - lastDecodeAt < 220) {
                liveFrameRequest = requestAnimationFrame(detectFromLiveFrame);
                return;
            }
            lastDecodeAt = now;

            try {
                if (liveDetector) {
                    const codes = await liveDetector.detect(liveVideo);
                    const value = codes[0]?.rawValue;

                    if (value && submitDecodedValue(value)) {
                        stopLiveScan();
                        return;
                    }
                } else if (typeof jsQR === 'function') {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d', { willReadFrequently: true });
                    const width = liveVideo.videoWidth || 0;
                    const height = liveVideo.videoHeight || 0;

                    if (width > 0 && height > 0) {
                        canvas.width = width;
                        canvas.height = height;
                        context.drawImage(liveVideo, 0, 0, width, height);
                        const imageData = context.getImageData(0, 0, width, height);
                        const result = jsQR(imageData.data, width, height, { inversionAttempts: 'attemptBoth' });

                        if (result?.data && submitDecodedValue(result.data)) {
                            stopLiveScan();
                            return;
                        }
                    }
                }
            } catch (error) {
                setStatus('Erreur de lecture caméra. Essayez à nouveau.');
            }

            if (liveScanRunning) {
                liveFrameRequest = requestAnimationFrame(detectFromLiveFrame);
            }
        };

        const startLiveScan = async () => {
            if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) {
                setStatus('Caméra non disponible sur ce navigateur.');
                return;
            }

            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                setStatus('Le scan caméra en direct nécessite HTTPS.');
                return;
            }

            try {
                stopLiveScan();

                if ('BarcodeDetector' in window) {
                    liveDetector = new BarcodeDetector({ formats: ['qr_code'] });
                } else {
                    liveDetector = null;
                }

                liveStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
                    },
                    audio: false,
                });

                liveVideo.srcObject = liveStream;
                await liveVideo.play();

                liveWrap.style.display = 'block';
                liveScanRunning = true;
                setCameraButtonState(true);
                setStatus('Caméra active. Placez le QR au centre.');
                detectFromLiveFrame();
            } catch (error) {
                stopLiveScan();
                setStatus('Impossible d’accéder à la caméra. Vérifiez les permissions.');
            }
        };

        const decodeFile = async (file) => {
            setStatus('Lecture de l’image en cours...');

            const nativeValue = await decodeWithBarcodeDetector(file);
            if (submitDecodedValue(nativeValue)) {
                return;
            }

            const reader = new FileReader();

            reader.onload = () => {
                const image = new Image();

                image.onload = () => {
                    const decoded = decodeWithJsQr(image);
                    if (submitDecodedValue(decoded)) {
                        return;
                    }

                    setStatus('Aucun QR code lisible trouvé dans cette image. Essayez une photo plus nette, sans reflet, en cadrant uniquement le QR.');
                    qrCodeField.focus();
                };

                image.onerror = () => {
                    setStatus('Impossible de lire cette image.');
                };

                image.src = reader.result;
            };

            reader.onerror = () => {
                setStatus('Erreur lors de la lecture du fichier.');
            };

            reader.readAsDataURL(file);
        };

        cameraButton.addEventListener('click', async () => {
            if (!isMobileDevice) {
                stopLiveScan();
                imageInput.click();
                return;
            }

            if (liveScanRunning) {
                stopLiveScan();
                setStatus('Caméra arrêtée.');
                return;
            }

            await startLiveScan();
        });

        imageInput.addEventListener('change', async () => {
            const file = imageInput.files && imageInput.files[0];

            if (!file) {
                setStatus('');
                return;
            }

            await decodeFile(file);
        });

        window.addEventListener('beforeunload', () => {
            stopLiveScan();
        });

        setCameraButtonState(false);
    </script>
@endsection
