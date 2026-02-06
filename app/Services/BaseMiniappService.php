<?php

namespace App\Services;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

abstract class BaseMiniappService
{
    protected string $baseUrl = 'https://so.miniapp.zone';
    protected string $shopPath;
    protected int $timeout = 15;
    protected bool $verify = true;

    public function __construct()
    {
        $this->baseUrl = $this->getEncryptedSetting(
            'settings.so_miniapp.base_uri_enc',
            config('services.so_miniapp.base_uri', 'https://so.miniapp.zone')
        );
        $this->baseUrl = rtrim($this->baseUrl, '/');

        $timeoutValue = Cache::get('settings.so_miniapp.timeout');
        $this->timeout = is_numeric($timeoutValue) ? (int) $timeoutValue : (int) config('services.so_miniapp.timeout', 15);

        $verify = Cache::get('settings.so_miniapp.verify');
        if ($verify === null) {
            $this->verify = (bool) config('services.so_miniapp.verify', true);
        } else {
            $this->verify = (bool) $verify;
        }
    }

    /**
     * Get encrypted setting or config
     */
    protected function getEncryptedSetting(string $key, string $default = ''): string
    {
        $raw = Cache::get($key);
        if (! is_string($raw) || $raw === '') {
            return $default;
        }

        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Parse raw cookie string into array
     */
    protected function parseCookieString(?string $cookie): array
    {
        $cookies = [];
        $cookie = trim((string) $cookie);
        if ($cookie === '') {
            return $cookies;
        }

        foreach (explode(';', $cookie) as $part) {
            $part = trim($part);
            if ($part === '' || ! str_contains($part, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $part, 2);
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $cookies[$name] = $value;
        }

        return $cookies;
    }

    /**
     * Extract CSRF token from HTML
     */
    protected function extractCsrfToken(string $html): ?string
    {
        // Try standard Laravel meta tag: <meta name="csrf-token" content="...">
        if (preg_match('/<meta\s+name=[\'"]csrf-token[\'"]\s+content=[\'"]([^\'"]+)[\'"]/i', $html, $m)) {
            $token = trim($m[1]);
            if ($token !== '') return $token;
        }

        // Try reverse order: <meta content="..." name="csrf-token">
        if (preg_match('/<meta\s+content=[\'"]([^\'"]+)[\'"]\s+name=[\'"]csrf-token[\'"]/i', $html, $m)) {
            $token = trim($m[1]);
            if ($token !== '') return $token;
        }

        return null;
    }

    /**
     * Get common HTTP options
     */
    protected function getHttpOptions(array $overrides = []): array
    {
        return array_merge([
            'verify' => $this->verify,
            'timeout' => $this->timeout,
            'allow_redirects' => true,
        ], $overrides);
    }

    /**
     * Create session with cookies and fetch CSRF token
     */
    protected function createSession(?string $cookieOverride = null): array
    {
        $jar = new CookieJar;

        // Priority:
        // 1. Explicit override
        // 2. Encrypted setting from Admin Panel (settings.so_miniapp.cookie_enc)
        // 3. Old ENV variable (MINIAPPZONE_COOKIE)
        // 4. New Config/ENV variable (services.so_miniapp.cookie / SO_MINIAPP_COOKIE)

        $cookieStr = $cookieOverride;

        if (! $cookieStr) {
            $cookieStr = $this->getEncryptedSetting('settings.so_miniapp.cookie_enc');
        }

        if (! $cookieStr) {
            $cookieStr = env('MINIAPPZONE_COOKIE');
        }

        if (! $cookieStr) {
            $cookieStr = config('services.so_miniapp.cookie');
        }

        $cookies = $this->parseCookieString($cookieStr);

        foreach ($cookies as $name => $value) {
            $jar->setCookie(new \GuzzleHttp\Cookie\SetCookie([
                'Name' => $name,
                'Value' => $value,
                'Domain' => 'so.miniapp.zone',
                'Path' => '/',
            ]));
        }

        $shopUrl = $this->baseUrl.$this->shopPath;
        $baseHeaders = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        // Fetch shop page to get CSRF token and session cookies
        // Add retry mechanism
        $csrf = null;
        $maxRetries = 5;
        
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $options = $this->getHttpOptions(['cookies' => $jar]);
                $resp = Http::withOptions($options)
                    ->withHeaders($baseHeaders)
                    ->get($shopUrl);

                if ($resp->successful()) {
                    $csrf = $this->extractCsrfToken((string) $resp->body());
                    if ($csrf) {
                        break;
                    } else {
                        \Illuminate\Support\Facades\Log::warning("BaseMiniappService: CSRF not found in attempt " . ($i+1));
                    }
                } else {
                     \Illuminate\Support\Facades\Log::warning("BaseMiniappService: HTTP " . $resp->status() . " in attempt " . ($i+1));
                }
                
                // If failed, wait a bit before retry
                if ($i < $maxRetries - 1) {
                    sleep(2);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("BaseMiniappService: Exception " . $e->getMessage() . " in attempt " . ($i+1));
                // If exception, wait and retry
                if ($i < $maxRetries - 1) {
                    sleep(2);
                }
            }
        }

        return [
            'cookieJar' => $jar,
            'csrf' => $csrf,
            'shopUrl' => $shopUrl,
            'baseHeaders' => $baseHeaders,
        ];
    }
}
