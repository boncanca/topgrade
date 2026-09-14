<?php

declare(strict_types=1);

namespace App\Services;

use Resend;

class ResendEmailService
{
    /**
     * Send a general email using Resend API client.
     *
     * @param  string|array<int, string>  $to
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendEmail(string|array $to, string $subject, string $htmlContent, ?string $from = null, array $options = []): array
    {
        $apiKey = (string) config('resend.api_key', env('RESEND_API_KEY'));

        if (empty($apiKey) || $apiKey === 're_xxxxxxxxx') {
            throw new \RuntimeException('Resend API key is missing. Please set RESEND_API_KEY in your .env file.');
        }

        $resend = Resend::client($apiKey);

        $defaultFrom = sprintf('%s <%s>', config('mail.from.name', 'TopGrade FC'), config('mail.from.address', 'onboarding@resend.dev'));

        $payload = array_merge([
            'from' => $from ?? $defaultFrom,
            'to' => is_array($to) ? $to : [$to],
            'subject' => $subject,
            'html' => $htmlContent,
        ], $options);

        /** @var object $response */
        $response = $resend->emails->send($payload);

        return (array) $response;
    }

    /**
     * Send a Magic Login Link email via Resend.
     */
    public function sendMagicLoginLink(string $recipientEmail, string $loginUrl): array
    {
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                <h2 style='color: #1e293b;'>Log in to ".e(config('app.name')).'</h2>
                <p style=\'color: #475569; font-size: 16px;\'>Click the button below to log in to your account. This link will expire shortly.</p>
                <div style=\'margin: 30px 0;\'>
                    <a href=\''.e($loginUrl)."' style='background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Log In to Your Account</a>
                </div>
                <p style='color: #94a3b8; font-size: 14px;'>If you did not request this login link, please ignore this email.</p>
            </div>
        ";

        return $this->sendEmail(
            to: $recipientEmail,
            subject: 'Your Magic Login Link - '.config('app.name'),
            htmlContent: $html
        );
    }

    /**
     * Send a 2FA Verification Code email via Resend.
     */
    public function sendTwoFactorCode(string $recipientEmail, string $code): array
    {
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                <h2 style='color: #1e293b;'>Two-Factor Authentication Code</h2>
                <p style=\'color: #475569; font-size: 16px;\'>Your one-time authentication code for ".e(config('app.name')).' is:</p>
                <div style=\'background-color: #f1f5f9; padding: 16px; border-radius: 6px; text-align: center; margin: 24px 0;\'>
                    <span style=\'font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #0f172a;\'>'.e($code)."</span>
                </div>
                <p style='color: #94a3b8; font-size: 14px;'>This code is valid for 10 minutes. Do not share it with anyone.</p>
            </div>
        ";

        return $this->sendEmail(
            to: $recipientEmail,
            subject: $code.' is your authentication code',
            htmlContent: $html
        );
    }

    /**
     * Send a Password Reset Link email via Resend.
     */
    public function sendPasswordReset(string $recipientEmail, string $resetUrl): array
    {
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                <h2 style='color: #1e293b;'>Reset Your Password</h2>
                <p style=\'color: #475569; font-size: 16px;\'>We received a request to reset your password for your ".e(config('app.name')).' account.</p>
                <div style=\'margin: 30px 0;\'>
                    <a href=\''.e($resetUrl)."' style='background-color: #dc2626; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Reset Password</a>
                </div>
                <p style='color: #94a3b8; font-size: 14px;'>If you did not request a password reset, no further action is required.</p>
            </div>
        ";

        return $this->sendEmail(
            to: $recipientEmail,
            subject: 'Reset Password Notification - '.config('app.name'),
            htmlContent: $html
        );
    }
}
