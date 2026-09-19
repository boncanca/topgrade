<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · @yield('title') | TopGrade London FC</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Anton&family=Archivo:wght@400;500;600;700&display=swap');
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background-color: #08040f;
            color: #f6f1fb;
            font-family: 'Archivo', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding-bottom: 1.5rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
        }

        .brand img {
            height: 36px;
            width: auto;
        }

        .brand-title {
            font-family: 'Anton', "Arial Narrow", sans-serif;
            text-transform: uppercase;
            font-size: 1.25rem;
            letter-spacing: 0.05em;
        }

        .main {
            max-width: 1100px;
            width: 100%;
            margin: 3rem auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            align-items: center;
        }

        @media (min-width: 768px) {
            .main {
                grid-template-columns: 1.3fr 0.7fr;
            }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: #05020a;
            color: #c41e9b;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .code {
            font-family: 'Anton', "Arial Narrow", sans-serif;
            font-size: clamp(5rem, 12vw, 8rem);
            line-height: 0.9;
            color: #ffffff;
            margin-bottom: 1rem;
        }

        .headline {
            font-family: 'Anton', "Arial Narrow", sans-serif;
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }

        .description {
            color: #a294b8;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .btn-primary {
            display: inline-block;
            background: #c41e9b;
            color: #ffffff;
            padding: 0.85rem 1.75rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border-radius: 2px;
            transition: background 0.2s ease;
        }

        .btn-primary:hover {
            background: #ff4fd0;
        }

        .pitch-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-top: 2px dashed rgba(255, 255, 255, 0.2);
            padding-top: 1.5rem;
        }

        .ball {
            width: 120px;
            height: 120px;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.8));
        }

        .touchline-label {
            margin-top: 1rem;
            font-size: 0.7rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #a294b8;
            font-family: monospace;
        }

        .footer {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #a294b8;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="/" class="brand">
            <img src="/logo.png" alt="TopGrade London FC Logo">
            <span class="brand-title">TopGrade London FC</span>
        </a>
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #a294b8;">
            Matchday Notice
        </span>
    </header>

    <main class="main">
        <div>
            <div class="badge">
                <span>●</span>
                <span>@yield('title')</span>
            </div>
            <div class="code">@yield('code')</div>
            <h1 class="headline">@yield('headline', 'Play has been stopped.')</h1>
            <p class="description">@yield('message', 'The requested matchday page is temporarily unavailable. Return to the pitch to continue.')</p>
            <a href="/" class="btn-primary">Back to the Club</a>
        </div>

        <div class="pitch-side">
            <img src="/ball-optimized.webp" alt="TopGrade match ball" class="ball">
            <div class="touchline-label">TOUCHLINE // PLAY STOPPED</div>
        </div>
    </main>

    <footer class="footer">
        <span>Official Matchday Control · TOPGRADE LONDON FC CIC</span>
        <span>© 2026 TopGrade London FC</span>
    </footer>
</body>
</html>
