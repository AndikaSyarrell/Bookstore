<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    protected function configure()
    {
        try {
            $this->mailer->isSMTP();

            // --- TAMBAHKAN INI UNTUK DEBUGGING ---
            $this->mailer->SMTPDebug = 2; // Output komunikasi client-server
            $this->mailer->Debugoutput = function ($str, $level) {
                Log::debug("SMTP Debug: $str");
            };
            // -------------------------------------

            $this->mailer->Host       = config('mail.mailers.smtp.host');
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = config('mail.mailers.smtp.username');
            $this->mailer->Password   = config('mail.mailers.smtp.password');
            $this->mailer->SMTPSecure = config('mail.mailers.smtp.encryption');
            $this->mailer->Port       = config('mail.mailers.smtp.port');

            // Encoding
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

            // From
            $this->mailer->setFrom(
                config('mail.from.address'),
                config('mail.from.name')
            );
        } catch (Exception $e) {
            Log::error('PHPMailer Configuration Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendPasswordResetEmail($email, $token, $userName = null)
    {
        try {

            // BERSIHKAN data penerima sebelumnya agar tidak menumpuk
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            // Recipients
            $this->mailer->addAddress($email, $userName);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Reset Password - ' . config('app.name');

            // Generate reset URL
            $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($email));

            // Email body
            $this->mailer->Body = $this->getResetEmailTemplate($resetUrl, $userName, $email);
            $this->mailer->AltBody = $this->getPlainTextTemplate($resetUrl);

            // Send
            $this->mailer->send();

            Log::info('Password reset email sent successfully', [
                'email' => $email,
                'token' => substr($token, 0, 10) . '...'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send password reset email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    protected function getResetEmailTemplate($resetUrl, $userName, $email)
    {
        $appName = config('app.name');
        $expiryMinutes = config('auth.passwords.users.expire', 60);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .alternative-link {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            word-break: break-all;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #666;
        }
        .alternative-link code {
            display: block;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            color: #333;
        }
        .info-box {
            margin: 30px 0;
            padding: 20px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .footer {
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            color: #666;
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Reset Password</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo, <strong>{$userName}</strong>
            </div>
            
            <div class="message">
                <p>Kami menerima permintaan untuk mereset password akun Anda yang terdaftar dengan email <strong>{$email}</strong>.</p>
                <p>Klik tombol di bawah ini untuk membuat password baru:</p>
            </div>

            <div class="button-container">
                <a href="{$resetUrl}" class="button">Reset Password</a>
            </div>

            <div class="info-box">
                <p><strong>⏱ Link ini akan kadaluarsa dalam {$expiryMinutes} menit.</strong></p>
            </div>

            <div class="alternative-link">
                <p>Jika tombol tidak berfungsi, salin dan tempel URL berikut ke browser Anda:</p>
                <code>{$resetUrl}</code>
            </div>

            <div class="message" style="margin-top: 30px;">
                <p><strong>Tidak merasa meminta reset password?</strong></p>
                <p>Jika Anda tidak melakukan permintaan ini, abaikan email ini. Password Anda tidak akan berubah.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2026 {$appName}. All rights reserved.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function getPlainTextTemplate($resetUrl)
    {
        $appName = config('app.name');
        $expiryMinutes = config('auth.passwords.users.expire', 60);

        return <<<TEXT
Reset Password - {$appName}

Kami menerima permintaan untuk mereset password akun Anda.

Klik link berikut untuk mereset password:
{$resetUrl}

Link ini akan kadaluarsa dalam {$expiryMinutes} menit.

Jika Anda tidak melakukan permintaan ini, abaikan email ini.

---
{$appName}
Email ini dikirim secara otomatis, mohon tidak membalas.
TEXT;
    }
}
