<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WebPushService;
use Illuminate\Http\Request;

class WebPushController extends Controller
{
    protected WebPushService $webPushService;

    public function __construct(WebPushService $webPushService)
    {
        $this->webPushService = $webPushService;
    }

    public function vapidPublicKey()
    {
        return response()->json(['publicKey' => $this->webPushService->getPublicKey()]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'subscription' => 'required|array',
            'subscription.endpoint' => 'required|string',
            'subscription.keys' => 'required|array',
            'subscription.keys.p256dh' => 'required|string',
            'subscription.keys.auth' => 'required|string',
        ]);

        $userId = $request->user()->id;

        try {
            $this->webPushService->subscribe($request->subscription, $userId);

            return response()->json(['message' => 'Suscrito correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al suscribir: '.$e->getMessage()], 500);
        }
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        try {
            $this->webPushService->unsubscribe($request->endpoint);

            return response()->json(['message' => 'Desuscrito correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al desuscribir: '.$e->getMessage()], 500);
        }
    }
}
