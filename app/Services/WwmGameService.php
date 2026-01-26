<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WwmGameService extends BaseMiniappService
{
    protected string $shopPath = '/shop/wwm';

    /**
     * Check WWM User ID
     * Uses /check-username endpoint as verified by shop.js analysis
     */
    public function checkId(string $gameId, string $serverId = '', ?string $cookieOverride = null): array
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
            'Content-Type' => 'application/json', // Required for this endpoint
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
        ]);

        // Payload for /check-username
        $payload = [
            'game_id' => $gameId,
            'server_id' => $serverId,
            'game' => 'wwm',
            'char_name' => '', // Corresponds to #sver input which is often empty for WWM
        ];

        try {
            $response = Http::withOptions($this->getHttpOptions(['cookies' => $jar]))
                ->withHeaders($headers)
                ->asJson()
                ->post($this->baseUrl.'/check-username', $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Check response code (200 is success)
                if (isset($data['code']) && $data['code'] === 200) {
                    return [
                        'success' => true,
                        'ok' => true, // Compatible with other controllers
                        'username' => $data['username'] ?? 'Unknown',
                        'nickname' => $data['username'] ?? 'Unknown', // Compatible with other controllers
                        'data' => $data,
                    ];
                }

                // Handle errors (e.g. 404)
                return [
                    'success' => false,
                    'ok' => false,
                    'message' => $data['message'] ?? 'User not found',
                    'raw' => $data,
                ];
            }

            return ['success' => false, 'ok' => false, 'status' => $response->status(), 'body' => $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Place Order for WWM
     */
    public function order(
        string $gameId,
        string $productId,
        string $serverId = '',
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

        // Standard payload fields for WWM order
        // Based on form analysis: <form action="https://so.miniapp.zone/order" ...>
        $payload = [
            '_token' => $csrf,
            'pmethod' => $pmethod,
            'game_id' => $gameId,
            'server_id' => $serverId, // Might be empty/ignored but safe to include
            'product_id' => $productId,
            'count' => (string) $count,
        ];

        // Add price/amount fields if provided
        if ($price) {
            $payload['show_price'] = $price;
        }
        if ($amount) {
            $payload['show_amount'] = $amount; // Usually recipient name or similar
        }

        $headers = array_merge($session['baseHeaders'], [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'X-CSRF-TOKEN' => $csrf,
            'Referer' => $session['shopUrl'],
            'Origin' => $this->baseUrl,
        ]);

        $orderUrl = $this->baseUrl.'/order';

        try {
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

            if ($resp->status() === 302 || in_array($resp->status(), [200, 201, 202, 303], true)) {
                return [
                    'ok' => true,
                    'redirect_url' => $resp->header('Location'),
                    'status' => $resp->status(),
                    'attempts' => $attempts,
                ];
            }

            // Check for success text in body if not redirecting (sometimes returns JSON or HTML)
            $body = $resp->body();
            if (str_contains($body, 'Order Successful') || str_contains($body, 'success')) {
                return ['ok' => true, 'body_preview' => substr($body, 0, 100), 'attempts' => $attempts];
            }

            return [
                'ok' => false,
                'status' => $resp->status(),
                'body_preview' => substr($body, 0, 500),
                'attempts' => $attempts,
            ];

        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
