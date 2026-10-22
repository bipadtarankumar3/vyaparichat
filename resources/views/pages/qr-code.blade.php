@extends('layouts.app')

@section('content')
<div x-data="qrCodeGenerator()" class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <span>🔲</span>
            <span>WhatsApp Click-to-Chat QR Code</span>
        </h1>
        <p class="text-xs text-slate-400 mt-1">Generate scan-and-chat QR codes for marketing materials, packaging, stickers, or store banners.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Configuration Card -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow">
            <h3 class="font-bold text-white text-base">QR Code Settings</h3>

            <div>
                <label class="block font-semibold text-slate-300 text-xs mb-1.5">WhatsApp Phone Number</label>
                <input type="text" x-model="phone" placeholder="e.g. 919876543210" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-brand-500">
                <p class="text-[11px] text-slate-500 mt-1">Include country code without + or spaces (e.g. 919876543210).</p>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 text-xs mb-1.5">Pre-filled Customer Message</label>
                <textarea x-model="presetMessage" rows="4" placeholder="e.g. Hi! I'm interested in your products and would like more details." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-brand-500 leading-relaxed"></textarea>
                <p class="text-[11px] text-slate-500 mt-1">This text will be automatically typed in the customer's WhatsApp chat when scanned.</p>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 text-xs mb-1.5">Direct Chat Link</label>
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-mono text-brand-300 break-all select-all" x-text="chatUrl"></div>
            </div>
        </div>

        <!-- Live QR Code Card -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 flex flex-col items-center justify-between text-center space-y-6 shadow">
            <div class="space-y-2">
                <span class="px-3 py-1 rounded-full bg-brand-500/10 text-brand-400 font-bold text-xs border border-brand-500/20">
                    Live Dynamic Preview
                </span>
                <h3 class="font-bold text-white text-base">Scan to Chat</h3>
            </div>

            <!-- QR Image Box -->
            <div class="p-4 rounded-2xl bg-white shadow-xl">
                <img :src="qrImageUrl" alt="WhatsApp QR Code" class="w-56 h-56 object-contain">
            </div>

            <div class="w-full flex items-center justify-center gap-3">
                <a :href="qrImageUrl" download="whatsapp-qr-code.png" target="_blank" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download QR Code</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function qrCodeGenerator() {
    return {
        phone: '{{ $vendor->phone ?? "919876543210" }}'.replace(/[^0-9]/g, ''),
        presetMessage: 'Hi! I found your store on VyapariChat and would like more information.',

        get chatUrl() {
            let cleanPhone = this.phone.replace(/[^0-9]/g, '');
            return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(this.presetMessage)}`;
        },

        get qrImageUrl() {
            return `https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=${encodeURIComponent(this.chatUrl)}`;
        }
    };
}
</script>
@endsection
