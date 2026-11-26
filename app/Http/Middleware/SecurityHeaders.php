<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add a strict Content Security Policy and related security headers to every web response.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce));
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        if ($request->is('admin', 'admin/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }

    /**
     * Build the Content Security Policy. While the Vite dev server runs, its origin is allowed as well.
     */
    private function contentSecurityPolicy(string $nonce): string
    {
        $devServer = $this->viteDevServerOrigin();
        $devSocket = $devServer === null ? null : preg_replace('/^http/', 'ws', $devServer);

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'nonce-{$nonce}'", $devServer],
            'style-src' => ["'self'", "'nonce-{$nonce}'", $devServer],
            'img-src' => ["'self'", 'data:', $devServer],
            'font-src' => ["'self'", $devServer],
            'connect-src' => ["'self'", $devServer, $devSocket],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
            'base-uri' => ["'self'"],
            'object-src' => ["'none'"],
        ];

        return collect($directives)
            ->map(fn (array $sources, string $directive) => $directive.' '.implode(' ', array_filter($sources)))
            ->implode('; ');
    }

    /**
     * Get the origin of the Vite dev server when it is running.
     */
    private function viteDevServerOrigin(): ?string
    {
        if (! Vite::isRunningHot()) {
            return null;
        }

        return rtrim(trim((string) file_get_contents(Vite::hotFile())), '/');
    }
}
