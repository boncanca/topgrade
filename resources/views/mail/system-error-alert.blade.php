<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TopGrade London FC - System Error Alert</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #0c0a09;
            color: #f5f5f4;
            margin: 0;
            padding: 24px;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
            background-color: #1c1917;
            border: 1px solid #292524;
            border-radius: 12px;
            overflow: hidden;
        }
        .header {
            background-color: #7f1d1d;
            border-bottom: 2px solid #b91c1c;
            padding: 20px 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #fef2f2;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .body {
            padding: 24px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #ef4444;
            color: #ffffff;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 13px;
        }
        .table td {
            padding: 8px 12px;
            border-bottom: 1px solid #292524;
        }
        .table td.label {
            width: 130px;
            color: #a8a29e;
            font-weight: 600;
        }
        .table td.value {
            color: #fafaf9;
            font-family: monospace;
            word-break: break-all;
        }
        .stack-box {
            margin-top: 20px;
            background-color: #0c0a09;
            border: 1px solid #292524;
            border-radius: 8px;
            padding: 16px;
            overflow-x: auto;
        }
        .stack-box pre {
            margin: 0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            color: #f87171;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .footer {
            padding: 16px 24px;
            background-color: #141210;
            border-top: 1px solid #292524;
            font-size: 12px;
            color: #78716c;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">Unhandled Exception</span>
            <h1 style="margin-top: 8px;">{{ $errorClass }}</h1>
        </div>

        <div class="body">
            <p style="margin-top: 0; color: #fca5a5; font-size: 14px; font-weight: 600;">
                {{ $errorMessage }}
            </p>

            <table class="table">
                <tr>
                    <td class="label">Environment</td>
                    <td class="value">{{ strtoupper($environment) }}</td>
                </tr>
                <tr>
                    <td class="label">Occurred At</td>
                    <td class="value">{{ $timestamp }}</td>
                </tr>
                <tr>
                    <td class="label">File & Line</td>
                    <td class="value">{{ $file }}:{{ $line }}</td>
                </tr>
                <tr>
                    <td class="label">HTTP Request</td>
                    <td class="value">{{ $method }} {{ $url }}</td>
                </tr>
                <tr>
                    <td class="label">Client IP</td>
                    <td class="value">{{ $ip }}</td>
                </tr>
                <tr>
                    <td class="label">Active User</td>
                    <td class="value">{{ $userEmail }}</td>
                </tr>
            </table>

            <div class="stack-box">
                <div style="color: #a8a29e; font-size: 11px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase;">
                    Stack Trace (Top 30 Frames)
                </div>
                <pre>{{ $stackTrace }}</pre>
            </div>
        </div>

        <div class="footer">
            Automated Exception Monitor · TopGrade London FC · Sent to {{ env('DEV_SUPPORT_EMAIL', 'support@trupabranding.com') }}
        </div>
    </div>
</body>
</html>
