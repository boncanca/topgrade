<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TopGrade') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-collapse: collapse;
        }
        .email-header {
            background-color: #1e40af;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-body {
            padding: 30px 20px;
        }
        .email-body h2 {
            color: #1e40af;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .email-body p {
            margin: 10px 0;
            line-height: 1.8;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1e40af;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background-color: #1e3a8a;
        }
        .email-footer {
            background-color: #f5f5f5;
            padding: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .detail-box {
            background-color: #f9fafb;
            padding: 15px;
            border-left: 4px solid #1e40af;
            margin: 15px 0;
            border-radius: 2px;
        }
        .detail-box strong {
            color: #1e40af;
        }
    </style>
</head>
<body>
    <table class="email-container" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td class="email-header">
                <h1>{{ config('app.name', 'TopGrade') }}</h1>
            </td>
        </tr>
        <tr>
            <td class="email-body">
                @yield('content')
            </td>
        </tr>
        <tr>
            <td class="email-footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'TopGrade') }}. All rights reserved.</p>
                <p>If you have any questions, please contact us at {{ config('mail.from.address') }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
