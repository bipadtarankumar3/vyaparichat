<?php

namespace App\Http\Middleware;

use App\Models\VendorSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithVendorApiKey
{
    /**
     * Handle an incoming request authenticated via Vendor API key.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY') ?: $request->bearerToken();

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Missing API Key. Provide via X-API-KEY header or Bearer token.',
            ], 401);
        }

        $setting = VendorSetting::where('key', 'api_key')
            ->where('value', $apiKey)
            ->first();

        if (!$setting || !$setting->vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired API Key.',
            ], 401);
        }

        $vendor = $setting->vendor;

        // Check if vendor has API access enabled in their plan
        $billingService = app(\App\Services\Billing\BillingService::class);
        $check = $billingService->checkFeatureLimit($vendor, 'api_access');
        if (!$check['allowed']) {
            return response()->json([
                'success' => false,
                'message' => 'API Access is not enabled for your current subscription plan.',
            ], 403);
        }

        $request->attributes->set('vendor', $vendor);

        return $next($request);
    }
}
