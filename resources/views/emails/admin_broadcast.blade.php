<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $emailSubject }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f5ede0; font-family: Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #FF6B35, #FFA500); padding: 32px 40px; text-align: center; }
        .header img { height: 40px; margin-bottom: 12px; }
        .header h1 { color: #fff; margin: 0; font-size: 1.6rem; font-weight: 800; letter-spacing: 1px; }
        .body { padding: 36px 40px; }
        .body p { color: #3d2b1f; font-size: 1rem; line-height: 1.7; white-space: pre-line; margin: 0; }
        .footer { background: #fdf3e7; padding: 20px 40px; text-align: center; border-top: 1px solid #f0e0cc; }
        .footer p { color: #a08060; font-size: 0.8rem; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>FOODYML</h1>
        </div>
        <div class="body">
            <p>{{ $body }}</p>
        </div>
        <div class="footer">
            <p>Šo e-pastu nosūtīja FOODYML administrators &bull; Lūdzu, neatbildēt uz šo ziņojumu.</p>
        </div>
    </div>
</body>
</html>
