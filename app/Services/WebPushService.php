<?php

namespace App\Services;

use Base64Url\Base64Url;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $webPush = null;

    protected ?string $publicKey = null;

    protected ?string $privateKey = null;

    protected bool $enabled = false;

    public function __construct()
    {
        $this->publicKey = config('webpush.public_key') ?? env('VAPID_PUBLIC_KEY');
        $this->privateKey = config('webpush.private_key') ?? env('VAPID_PRIVATE_KEY');
        $this->enabled = $this->validateKeys();

        if ($this->enabled) {
            $this->webPush = new WebPush([
                'VAPID' => [
                    'subject' => env('VAPID_SUBJECT', 'mailto:noreply@catedralcristiana.com'),
                    'publicKey' => $this->publicKey,
                    'privateKey' => $this->privateKey,
                ],
            ]);
        }
    }

    protected function validateKeys(): bool
    {
        if (empty($this->publicKey) || empty($this->privateKey)) {
            return false;
        }

        try {
            $publicKey = Base64Url::decode($this->publicKey);
            $privateKey = Base64Url::decode($this->privateKey);

            return strlen($publicKey) === 65 && strlen($privateKey) === 32;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPublicKey(): string
    {
        return $this->publicKey ?? '';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function subscribe(array $subscriptionData, int $userId): bool
    {
        if (! $this->enabled) {
            return false;
        }

        $subscription = Subscription::create([
            'endpoint' => $subscriptionData['endpoint'],
            'publicKey' => $subscriptionData['keys']['p256dh'],
            'authToken' => $subscriptionData['keys']['auth'],
        ]);

        DB::table('notification_subscriptions')->updateOrInsert(
            ['endpoint' => $subscriptionData['endpoint']],
            [
                'user_id' => $userId,
                'endpoint' => $subscriptionData['endpoint'],
                'p256dh' => $subscriptionData['keys']['p256dh'],
                'auth' => $subscriptionData['keys']['auth'],
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return true;
    }

    public function unsubscribe(string $endpoint): bool
    {
        DB::table('notification_subscriptions')
            ->where('endpoint', $endpoint)
            ->delete();

        return true;
    }

    public function sendNotification(int $userId, array $payload): int
    {
        if (! $this->enabled || ! $this->webPush) {
            return 0;
        }

        $subscriptions = DB::table('notification_subscriptions')
            ->where('user_id', $userId)
            ->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $sent = 0;
        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh,
                'authToken' => $sub->auth,
            ]);

            try {
                $this->webPush->sendNotification($subscription, json_encode($payload));
                $sent++;
            } catch (\Exception $e) {
                Log::warning('WebPush failed', [
                    'user_id' => $userId,
                    'endpoint' => $sub->endpoint,
                    'error' => $e->getMessage(),
                ]);

                if (str_contains($e->getMessage(), '410') || str_contains($e->getMessage(), '404')) {
                    DB::table('notification_subscriptions')
                        ->where('endpoint', $sub->endpoint)
                        ->delete();
                }
            }
        }

        $this->webPush->flush();

        return $sent;
    }

    public function sendToAll(array $payload): int
    {
        if (! $this->enabled || ! $this->webPush) {
            return 0;
        }

        $subscriptions = DB::table('notification_subscriptions')->get();
        $sent = 0;

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh,
                'authToken' => $sub->auth,
            ]);

            try {
                $this->webPush->sendNotification($subscription, json_encode($payload));
                $sent++;
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), '410') || str_contains($e->getMessage(), '404')) {
                    DB::table('notification_subscriptions')
                        ->where('endpoint', $sub->endpoint)
                        ->delete();
                }
            }
        }

        $this->webPush->flush();

        return $sent;
    }

    public function sendToSubscriptions(array $endpoints, array $payload): int
    {
        if (! $this->enabled || ! $this->webPush) {
            return 0;
        }

        $subscriptions = DB::table('notification_subscriptions')
            ->whereIn('endpoint', $endpoints)
            ->get();

        $sent = 0;
        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh,
                'authToken' => $sub->auth,
            ]);

            try {
                $this->webPush->sendNotification($subscription, json_encode($payload));
                $sent++;
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), '410') || str_contains($e->getMessage(), '404')) {
                    DB::table('notification_subscriptions')
                        ->where('endpoint', $sub->endpoint)
                        ->delete();
                }
            }
        }

        $this->webPush->flush();

        return $sent;
    }
}
