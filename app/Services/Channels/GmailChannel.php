<?php

namespace App\Services\Channels;

use App\Contracts\NotificationChannel;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GmailChannel implements NotificationChannel
{
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $template = $options['template'] ?? null;
        $metadata = $options['metadata'] ?? null;

        try {
            $accessToken = $this->refreshAccessToken();

            $raw = $this->buildMimeMessage(
                from: config('notifications.providers.gmail.from_address'),
                fromName: config('notifications.channels.email.from_name'),
                to: $to,
                subject: $subject,
                body: $body,
            );

            $response = Http::withToken($accessToken)
                ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                    'raw' => $raw,
                ]);

            if ($response->successful()) {
                Log::info('Email sent via Gmail API.', ['to' => $to, 'subject' => $subject, 'id' => $response->json('id')]);

                EmailLog::create([
                    'recipient' => $to,
                    'subject'   => $subject,
                    'template'  => $template,
                    'channel'   => 'email',
                    'status'    => 'sent',
                    'metadata'  => $metadata,
                    'sent_at'   => now(),
                ]);

                return true;
            }

            $errorBody = $response->body();
            Log::error('Gmail API error.', ['status' => $response->status(), 'body' => $errorBody]);

            EmailLog::create([
                'recipient'     => $to,
                'subject'       => $subject,
                'template'      => $template,
                'channel'       => 'email',
                'status'        => 'failed',
                'error_message' => "HTTP {$response->status()}: {$errorBody}",
                'metadata'      => $metadata,
                'sent_at'       => now(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('Gmail send failed.', ['error' => $e->getMessage(), 'to' => $to]);

            EmailLog::create([
                'recipient'     => $to,
                'subject'       => $subject,
                'template'      => $template,
                'channel'       => 'email',
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'metadata'      => $metadata,
                'sent_at'       => now(),
            ]);

            return false;
        }
    }

    private function refreshAccessToken(): string
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id'     => config('notifications.providers.gmail.client_id'),
            'client_secret' => config('notifications.providers.gmail.client_secret'),
            'refresh_token' => config('notifications.providers.gmail.refresh_token'),
            'grant_type'    => 'refresh_token',
        ]);

        if (! $response->successful() || ! $response->json('access_token')) {
            throw new \RuntimeException('Failed to refresh Gmail access token: ' . $response->body());
        }

        return $response->json('access_token');
    }

    private function buildMimeMessage(string $from, string $fromName, string $to, string $subject, string $body): string
    {
        $message = implode("\r\n", [
            "From: {$fromName} <{$from}>",
            "To: {$to}",
            "Subject: {$subject}",
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
            '',
            base64_encode($body),
        ]);

        // Gmail requires base64url encoding (RFC 4648)
        return rtrim(strtr(base64_encode($message), '+/', '-_'), '=');
    }
}
