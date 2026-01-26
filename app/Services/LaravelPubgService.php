<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LaravelPubgService extends BaseMiniappService
{
    protected string $shopPath = '/shop/pubg';

    public function checkId(string $gameId, string $serverId = '1', ?string $cookieString = null): array
    {
        try {
            $session = $this->createSession($cookieString);
            $csrf = $session['csrf'];

            if (! $csrf) {
                return ['success' => false, 'message' => 'Failed to extract CSRF token'];
            }

            // PUBG name-check expects JSON payload
            $response = Http::withOptions($this->getHttpOptions(['cookies' => $session['cookieJar']]))
                ->withHeaders(array_merge($session['baseHeaders'], [
                    'X-CSRF-TOKEN' => $csrf,
                    'Referer' => $session['shopUrl'],
                    'Origin' => $this->baseUrl,
                ]))
                ->post($this->baseUrl.'/name-check', [
                    'game_id' => $gameId,
                    'server_id' => $serverId,
                    'game' => 'pubg',
                ]);

            $data = $response->json();
            if (! is_array($data)) {
                return ['success' => false, 'message' => 'Invalid response'];
            }

            // Normalize response
            if (isset($data['code']) && $data['code'] === 200) {
                $data['success'] = true;
                $data['result'] = 1; // For backward compatibility
                $data['nickname'] = $data['username'] ?? $data['nickname'] ?? 'Unknown';
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('PUBG checkId error: '.$e->getMessage());

            return ['success' => false, 'message' => 'Request failed'];
        }
    }

    public function order(string $gameId, string $productId, string $serverId = '1', ?string $cookieString = null): array
    {
        try {
            $session = $this->createSession($cookieString);
            $csrf = $session['csrf'];

            if (! $csrf) {
                return ['success' => false, 'message' => 'Failed to extract CSRF token'];
            }

            $payload = [
                '_token' => $csrf,
                'pmethod' => 'usecoin',
                'game_id' => $gameId,
                'server_id' => $serverId,
                'product_id' => $productId,
                'count' => '1',
            ];

            $orderUrl = $this->baseUrl.'/order';
            
            // Order endpoint expects Form Data
            $response = Http::withOptions($this->getHttpOptions([
                'cookies' => $session['cookieJar'],
                'allow_redirects' => false,
            ]))
                ->withHeaders(array_merge($session['baseHeaders'], [
                    'X-CSRF-TOKEN' => $csrf,
                    'Referer' => $session['shopUrl'],
                    'Origin' => $this->baseUrl,
                ]))
                ->asForm()
                ->post($orderUrl, $payload);

            $statusCode = $response->status();
            $responseBody = (string) $response->body();

            if (in_array($statusCode, [200, 201, 302], true)) {
                return [
                    'success' => true,
                    'status' => $statusCode,
                    'message' => 'Order request sent',
                    'data' => $responseBody,
                ];
            }

            return [
                'success' => false,
                'status' => $statusCode,
                'message' => 'Order failed',
                'data' => $responseBody,
            ];
        } catch (\Throwable $e) {
            Log::error('PUBG order error: '.$e->getMessage());

            return ['success' => false, 'message' => 'Request failed'];
        }
    }
}
