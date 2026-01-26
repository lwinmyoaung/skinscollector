<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SoGameService extends BaseMiniappService
{
    protected string $shopPath = '/shop/mlbb';

    /**
     * Buy a product from SO Game Shop
     *
     * @param  string  $gameId  User's Game ID
     * @param  string  $serverId  User's Server/Zone ID
     * @param  string  $productId  Product ID
     * @param  string|null  $cookieString  The raw cookie string (optional)
     * @return array Response data
     */
    public function buyProduct($gameId, $serverId, $productId, $cookieString = null)
    {
        // 1. Create Session (Cookies + CSRF)
        $session = $this->createSession($cookieString);
        $csrf = $session['csrf'];

        if (! $csrf) {
            return [
                'success' => false,
                'message' => 'Failed to extract CSRF token from shop page',
            ];
        }

        // 2. Prepare Payload
        $payload = [
            '_token' => $csrf,
            'pmethod' => 'usecoin', // Verify if this should be 'mmk' or 'coins' based on site behavior
            'game_id' => $gameId,
            'server_id' => $serverId,
            'product_id' => $productId,
            'count' => '1',
        ];

        // 3. Send Purchase Request
        $orderUrl = $this->baseUrl.'/order';
        $headers = array_merge($session['baseHeaders'], [
            'X-CSRF-TOKEN' => $csrf,
            'Referer' => $session['shopUrl'],
            'Origin' => $this->baseUrl,
        ]);

        try {
            $response = Http::withOptions($this->getHttpOptions([
                'cookies' => $session['cookieJar'],
                'allow_redirects' => false, // Don't follow redirect automatically to check 302
            ]))
                ->withHeaders($headers)
                ->asForm()
                ->post($orderUrl, $payload);

            $statusCode = $response->status();
            $responseBody = (string) $response->body();

            // 4. Check Result
            // 302 Redirect is expected behavior for this shop
            if (in_array($statusCode, [200, 201, 302])) {
                return [
                    'success' => true,
                    'status' => $statusCode,
                    'message' => 'Request sent (Redirected). Check game/account for delivery.',
                    'data' => $responseBody,
                ];
            } else {
                return [
                    'success' => false,
                    'status' => $statusCode,
                    'message' => 'Purchase failed',
                    'data' => $responseBody,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Purchase Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check Role / Verify Game Account
     *
     * @param  string  $gameId
     * @param  string  $serverId
     * @return array|null Returns ['username' => '...', 'region' => '...'] or null if invalid
     */
    public function checkRole($gameId, $serverId)
    {
        try {
            // 1. Get Session
            // We use the standard session creation which might include admin cookies.
            // This is generally safe and consistent with other services.
            $session = $this->createSession();
            $csrf = $session['csrf'];

            if (! $csrf) {
                Log::error('MLBB Service: Could not extract CSRF token');

                return null;
            }

            // 2. Perform the Name Check
            $checkResponse = Http::withHeaders(array_merge($session['baseHeaders'], [
                'X-CSRF-TOKEN' => $csrf,
                'Content-Type' => 'application/json',
                'Referer' => $session['shopUrl'],
                'Origin' => $this->baseUrl,
            ]))
                ->withOptions($this->getHttpOptions(['cookies' => $session['cookieJar']]))
                ->post($this->baseUrl.'/name-check', [
                    'game_id' => (string) $gameId,
                    'server_id' => (string) $serverId,
                    'game' => 'mlbb',
                ]);

            if ($checkResponse->successful()) {
                $data = $checkResponse->json();

                // Check if code is 200 (Success)
                if (isset($data['code']) && $data['code'] == 200) {
                    return [
                        'status' => true,
                        'username' => $data['username'] ?? $data['name'] ?? 'Unknown',
                        'region' => $data['region'] ?? $data['server'] ?? 'Unknown',
                        'raw' => $data,
                    ];
                }
            }

            Log::warning("MLBB Check Failed for $gameId($serverId): ".$checkResponse->body());

            return null;

        } catch (\Exception $e) {
            Log::error('MLBB Service Error: '.$e->getMessage());

            return null;
        }
    }
}
