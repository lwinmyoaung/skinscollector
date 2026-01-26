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
        if (! preg_match('/<meta\\s+name=[\'"]csrf-token[\'"]\\s+content=[\'"]([^\'"]+)[\'"]/i', $html, $m)) {
            return null;
        }
        $token = trim($m[1]);

        return $token !== '' ? $token : null;
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
        $options = $this->getHttpOptions(['cookies' => $jar]);
        $resp = Http::withOptions($options)
            ->withHeaders($baseHeaders)
            ->get($shopUrl);

        $csrf = $resp->successful() ? $this->extractCsrfToken((string) $resp->body()) : null;

        return [
            'cookieJar' => $jar,
            'csrf' => $csrf,
            'shopUrl' => $shopUrl,
            'baseHeaders' => $baseHeaders,
        ];
    }
}
