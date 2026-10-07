<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
            background-color: #f0f0f4;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .card {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            padding: 40px 30px;
            max-width: 440px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 86px;
            color: #434AFA;
            margin: 0;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
        }

        h2 {
            font-size: 22px;
            color: #333333;
            margin: 20px 0 15px;
            font-weight: 700;
        }

        p.subtitle {
            color: #6b7280;
            font-size: 15px;
            margin-bottom: 30px;
            line-height: 1.5;
            padding: 0 10px;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            outline: none;
        }

        .btn-primary {
            background-color: #434AFA;
            color: white;
            border: 3px solid #b8bcff;
            box-shadow: 0 2px 10px rgba(67, 74, 250, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #3137d1;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: white;
            color: #374151;
            border: 1.5px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        .info-box {
            background-color: #fafaff;
            border: 1.5px dashed #e0dced;
            border-radius: 16px;
            padding: 24px;
            text-align: left;
            font-size: 13.5px;
            color: #555;
            line-height: 1.6;
        }

        .info-box h3 {
            color: #434AFA;
            font-size: 14.5px;
            margin-top: 0;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .info-box strong {
            color: #374151;
        }
        
        .info-box a {
            color: #434AFA;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 2px;
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>419</h1>
        <h2>Session expired</h2>
        <p class="subtitle">Your session has expired. Please refresh and log in again.</p>

        <div class="buttons">
            <a href="/login" class="btn btn-primary">
                <span style="font-size:18px; margin-right:6px">🔓</span> Login again
            </a>
            <button onclick="window.location.reload();" class="btn btn-secondary">
                <span style="font-size:18px; margin-right:6px">🔄</span> Refresh
            </button>
        </div>

        <div class="info-box">
            <h3>Seeing old data or layout?</h3>
            <p style="margin-top: 0; margin-bottom: 12px;">We just updated the app. If a page looks broken, please <strong style="color: #434AFA;">hard refresh</strong>:</p>
            <p style="margin: 6px 0;"><strong>Phone:</strong> Settings &rarr; Clear browsing data &rarr; Cached images &amp; files &rarr; Clear</p>
            <p style="margin: 6px 0;"><strong>Computer:</strong> Press <strong style="color: #434AFA;">Ctrl + Shift + R</strong> (Windows) or <strong style="color: #434AFA;">Cmd + Shift + R</strong> (Mac), or clear site data.</p>
            <p style="margin-top: 12px; margin-bottom: 0;">You can also try: <a href="#" onclick="window.location.reload(true);">Clear cache & reload</a></p>
        </div>
    </div>

</body>
</html>
