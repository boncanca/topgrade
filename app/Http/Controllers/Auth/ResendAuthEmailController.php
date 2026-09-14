<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ResendEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendAuthEmailController extends Controller
{
    public function __construct(
        protected ResendEmailService $resendEmailService
    ) {}

    /**
     * Test sending a standard welcome email via Resend client.
     */
    public function sendWelcomeEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $recipient = (string) $request->input('email');

        $result = $this->resendEmailService->sendEmail(
            to: $recipient,
            subject: 'Welcome to '.config('app.name'),
            htmlContent: '<p>Congrats on sending your <strong>first email</strong> with Resend!</p>'
        );

        return response()->json([
            'message' => 'Welcome email sent successfully via Resend API.',
            'response' => $result,
        ]);
    }

    /**
     * Send a Magic Login Link email.
     */
    public function sendMagicLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $recipient = (string) $request->input('email');
        $magicLink = url('/login/magic?token='.bin2hex(random_bytes(16)));

        $result = $this->resendEmailService->sendMagicLoginLink($recipient, $magicLink);

        return response()->json([
            'message' => 'Magic login link sent via Resend.',
            'response' => $result,
        ]);
    }

    /**
     * Send a 2FA code email.
     */
    public function sendTwoFactorCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $recipient = (string) $request->input('email');
        $code = (string) random_int(100000, 999999);

        $result = $this->resendEmailService->sendTwoFactorCode($recipient, $code);

        return response()->json([
            'message' => '2FA verification code sent via Resend.',
            'response' => $result,
        ]);
    }
}
