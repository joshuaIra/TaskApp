<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnforceHttpsFromSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment(['local', 'testing']) || $request->isSecure()) {
            return $next($request);
        }

        $enforceHttps = Cache::remember('setting_enforce_https', 60, function () {
            return filter_var(Setting::where('key', 'enforce_https')->value('value') ?? false, FILTER_VALIDATE_BOOLEAN);
        });

        if ($enforceHttps) {
            return redirect()->to('https://'.$request->getHttpHost().$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
