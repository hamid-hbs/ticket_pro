<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre billet est prêt</title>
    <style>
        @media only screen and (max-width: 520px) {
            .email-shell {
                padding: 12px !important;
            }

            .hero-block,
            .content-card {
                padding: 16px !important;
                border-radius: 16px !important;
            }

            .hero-title {
                font-size: 1.45rem !important;
                line-height: 1.2 !important;
            }

            .hero-text,
            .summary-text,
            .qr-help,
            .footer-note {
                font-size: 0.9rem !important;
            }

            .summary-table {
                font-size: 0.88rem !important;
            }

            .qr-frame {
                padding: 12px !important;
            }

            .qr-image {
                max-width: 190px !important;
            }

            .code-value {
                font-size: 0.74rem !important;
                padding: 10px 12px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background: linear-gradient(180deg, rgba(31, 111, 92, 0.22) 0%, #F2F2F2 68%, rgba(31, 111, 92, 0.10) 100%); font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; line-height: 1.6; color: #0F3F35;">
    @php
        $eventDate = $ticket->event?->date
            ? \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y')
            : 'Date non définie';
    @endphp
    <div class="email-shell" style="max-width: 640px; margin: 0 auto; padding: 26px 16px 36px;">
        <div class="hero-block" style="background: linear-gradient(135deg, #1F6F5C 0%, #0F3F35 100%); color: #F2F2F2; border-radius: 22px; padding: 26px 24px; box-shadow: 0 18px 40px rgba(15, 63, 53, 0.22); border: 1px solid rgba(242, 242, 242, 0.15);">
            <div style="font-size: 0.78rem; letter-spacing: 0.14em; text-transform: uppercase; color: #F2F2F2;">Ticket Pro</div>
            <h1 class="hero-title" style="margin: 8px 0 0; font-size: 1.85rem; line-height: 1.15; color: #F2F2F2;">Votre billet est prêt</h1>
            <p class="hero-text" style="margin: 12px 0 0; font-size: 1rem; color: #F2F2F2;">Bonjour <strong>{{ $ticket->name }}</strong>, votre billet a été émis avec succès.</p>
        </div>

        <div class="content-card" style="background: #F2F2F2; margin-top: 16px; border-radius: 20px; padding: 22px; border: 1px solid rgba(15, 63, 53, 0.12); box-shadow: 0 12px 28px rgba(15, 63, 53, 0.12);">
            <p class="summary-text" style="margin: 0 0 14px; color: #0F3F35; font-size: 0.97rem;">Voici le récapitulatif de votre billet :</p>

            <table role="presentation" class="summary-table" style="width: 100%; border-collapse: collapse; font-size: 0.95rem; margin-bottom: 18px;">
                <tr>
                    <td style="padding: 8px 0; color: #1F6F5C; width: 34%; font-weight: 700;">Email</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #0F3F35;">{{ $ticket->email }}</td>
                </tr>
                @if($ticket->event)
                    <tr>
                        <td style="padding: 8px 0; color: #1F6F5C; font-weight: 700;">Événement</td>
                        <td style="padding: 8px 0; font-weight: 600; color: #0F3F35;">{{ $ticket->event->title }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #1F6F5C; font-weight: 700;">Date</td>
                        <td style="padding: 8px 0; font-weight: 600; color: #0F3F35;">{{ $eventDate }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #1F6F5C; font-weight: 700;">Lieu</td>
                        <td style="padding: 8px 0; font-weight: 600; color: #0F3F35;">{{ $ticket->event->location }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; color: #1F6F5C; font-weight: 700;">Référence</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #0F3F35;">{{ $ticket->payment_reference ?? '—' }}</td>
                </tr>
            </table>

            <div class="qr-frame" style="background: linear-gradient(180deg, rgba(31, 111, 92, 0.10) 0%, rgba(242, 242, 242, 0.95) 100%); border: 1px solid rgba(31, 111, 92, 0.18); border-radius: 18px; padding: 16px; text-align: center;">
                <div style="font-size: 0.94rem; font-weight: 700; color: #0F3F35; margin-bottom: 10px;">QR code du billet</div>
                <img src="{{ $message->embedData($qrPng, 'qrcode-inline.png', 'image/png') }}"
                     alt="QR code — code billet {{ $ticket->qr_code }}"
                     class="qr-image"
                     style="display: block; width: 100%; max-width: 250px; height: auto; margin: 0 auto; background: #F2F2F2; border: 2px solid rgba(15, 63, 53, 0.20); border-radius: 14px; box-shadow: 0 8px 18px rgba(15, 63, 53, 0.10);">
                <p class="qr-help" style="font-size: 0.84rem; color: #0F3F35; margin: 12px 0 0;">Scannez cette image ou ouvrez la pièce jointe PNG.</p>
            </div>

            <div style="margin-top: 18px;">
                <div style="font-size: 0.88rem; font-weight: 700; color: #1F6F5C; margin-bottom: 8px;">Code billet</div>
                <div class="code-value" style="font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 0.8rem; background: #1F6F5C; border-radius: 14px; padding: 12px 14px; word-break: break-all; letter-spacing: 0.04em; color: #F2F2F2; user-select: all;">
                    {{ $ticket->qr_code }}
                </div>
            </div>

            <p class="footer-note" style="font-size: 0.84rem; color: #0F3F35; margin: 16px 0 0;">Une copie <strong>PDF</strong> complète est jointe à cet email.</p>
        </div>
    </div>
</body>
</html>
