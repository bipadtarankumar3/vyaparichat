@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">E-Commerce & Developer REST API v1</h1>
        <p class="text-xs text-slate-400 mt-1">Integrate Shopify, WooCommerce, Zoho, Salesforce, or custom backends with VyapariChat.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Developer API Key -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow">
            <div class="flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-brand-500/20 text-brand-400 font-bold text-lg">🔑</span>
                <div>
                    <h3 class="font-bold text-white text-base">Public REST API Key</h3>
                    <p class="text-xs text-slate-400">Authenticate requests via X-API-KEY header.</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 font-mono text-xs text-emerald-400 break-all select-all">
                {{ $vendor->getApiKey() ?: 'vyapari_live_' . md5($vendor->id . 'vyapari_secret') }}
            </div>

            <div class="space-y-2">
                <div class="text-xs font-semibold text-slate-300">Quick cURL Example:</div>
                <pre class="p-4 rounded-xl bg-slate-950 text-slate-300 text-[11px] font-mono overflow-x-auto border border-slate-800 leading-relaxed">
curl -X POST "{{ url('/api/v1/send-message') }}" \
  -H "X-API-KEY: {{ $vendor->getApiKey() ?: 'YOUR_API_KEY' }}" \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "919876543210",
    "type": "text",
    "message": "Hello from VyapariChat API!"
  }'</pre>
            </div>
        </div>

        <!-- E-Commerce Webhooks Setup -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow">
            <div class="flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-blue-500/20 text-blue-400 font-bold text-lg">🛍️</span>
                <div>
                    <h3 class="font-bold text-white text-base">Store Automation Webhooks</h3>
                    <p class="text-xs text-slate-400">Copy these URLs into your Shopify or WooCommerce webhook admin settings.</p>
                </div>
            </div>

            <div class="space-y-3.5 text-xs">
                <div>
                    <div class="text-slate-400 font-semibold mb-1 flex items-center justify-between">
                        <span>Shopify: Order Created</span>
                        <span class="text-[10px] text-emerald-400 font-mono">POST</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 break-all select-all">
                        {{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/shopify/orders-create') }}
                    </div>
                </div>

                <div>
                    <div class="text-slate-400 font-semibold mb-1 flex items-center justify-between">
                        <span>Shopify: Order Fulfilled / Tracking</span>
                        <span class="text-[10px] text-emerald-400 font-mono">POST</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 break-all select-all">
                        {{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/shopify/orders-fulfill') }}
                    </div>
                </div>

                <div>
                    <div class="text-slate-400 font-semibold mb-1 flex items-center justify-between">
                        <span>WooCommerce: Order Webhook</span>
                        <span class="text-[10px] text-emerald-400 font-mono">POST</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 break-all select-all">
                        {{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/woocommerce/orders') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
