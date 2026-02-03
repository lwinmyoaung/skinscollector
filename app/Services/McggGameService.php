<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class McggGameService extends BaseMiniappService
{
    protected string $shopPath = '/shop/mcgg';

    /**
     * Check MCGG User ID
     */
    public function checkId(string $gameId, string $serverId, ?string $cookieOverride = null): array
    {
        $session = $this->createSession($cookieOverride);
        $csrf = $session['csrf'];

        if (! $csrf) {
            return ['success' => false, 'message' => 'Failed to fetch CSRF token'];
        }

        $jar = $session['cookieJar'];
        $headers = array_merge($session['baseHeaders'], [
            'X-CSRF-TOKEN' => $csrf,
            'Referer' => $session['shopUrl'],
            'Origin' => $this->baseUrl,
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
        ]);

        $response = Http::withOptions($this->getHttpOptions(['cookies' => $jar]))
            ->withHeaders($headers)
            ->post($this->baseUrl.'/check-user-mcgg', [
                'game_id' => $gameId,
                'server_id' => $serverId,
                '_token' => $csrf,
            ]);

        if ($response->successful()) {
            // Parse the nested JSON in "response" key if present
            $data = $response->json();
            if (isset($data['response']) && is_string($data['response'])) {
                $nested = json_decode($data['response'], true);
                if (isset($nested['code']) && $nested['code'] === 200) {
                    return ['ok' => true, 'success' => true, 'nickname' => $nested['nickname'] ?? 'Unknown', 'data' => $nested];
                }

                return ['ok' => false, 'success' => false, 'message' => $nested['info'] ?? 'Unknown error', 'raw' => $nested];
            }

            return ['ok' => false, 'success' => false, 'message' => 'Invalid response format', 'raw' => $data];
        }

        return ['ok' => false, 'success' => false, 'status' => $response->status(), 'body' => $response->body()];
    }

    /**
     * Place Order for MCGG
     */
    public function order(
        string $gameId,
        string $serverId,
        string $productId,
        ?string $price = null,
        ?string $amount = null,
        ?string $cookieOverride = null,
        string $pmethod = 'usecoin',
        int $count = 1
    ): array {
        $session = $this->createSession($cookieOverride);
        $csrf = $session['csrf'];

        if (! $csrf) {
            return ['ok' => false, 'error' => 'Failed to extract CSRF token', 'shop_url' => $session['shopUrl']];
        }

        if ($count <= 0) {
            $count = 1;
        }

        // Standard payload fields
        $payload = [
            '_token' => $csrf,
            'pmethod' => $pmethod,
            'game_id' => $gameId,
            'server_id' => $serverId,
            'product_id' => $productId,
            'count' => (string) $count,
        ];

        // Add price/amount fields if provided (often required for validation)
        if ($price) {
            $payload['price'] = $price;
            $payload['show_price'] = $price;
        }
        if ($amount) {
            $payload['amount'] = $amount;
            $payload['show_amount'] = $amount;
        }

        $headers = array_merge($session['baseHeaders'], [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'X-CSRF-TOKEN' => $csrf,
            'Referer' => $session['shopUrl'],
            'Origin' => $this->baseUrl,
        ]);

        // Verified endpoint for MCGG orders
        $orderUrl = $this->baseUrl.'/order';

        $resp = Http::withOptions($this->getHttpOptions([
            'cookies' => $session['cookieJar'],
            'allow_redirects' => false,
        ]))
            ->withHeaders($headers)
            ->asForm()
            ->post($orderUrl, $payload);

        $attempts = [
            [
                'order_url' => $orderUrl,
                'status' => $resp->status(),
                'location' => $resp->header('Location'),
                'body_prefix' => mb_substr((string) $resp->body(), 0, 400),
            ],
        ];

        // Successful redirect usually indicates order creation
        if (in_array($resp->status(), [200, 201, 202, 302, 303], true)) {
            // Check for redirect to login page which indicates session failure
            if ($resp->status() === 302) {
                $location = $resp->header('Location');
                if ($location && (str_contains($location, 'login') || str_contains($location, 'auth'))) {
                    return [
                        'ok' => false,
                        'stage' => 'session_check',
                        'error' => 'Session expired or invalid cookie. Redirected to login.',
                        'attempts' => $attempts,
                    ];
                }
            }

            return [
                'ok' => true,
                'shop_url' => $session['shopUrl'],
                'order_url' => $orderUrl,
                'status' => $resp->status(),
                'location' => $resp->header('Location'),
                'attempts' => $attempts,
            ];
        }

        return [
            'ok' => false,
            'stage' => 'post_order',
            'shop_url' => $session['shopUrl'],
            'attempts' => $attempts,
        ];
    }
}
