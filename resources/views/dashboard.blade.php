@extends('layouts.app')

@section('content')
<div x-data="vyapariApp()" x-init="init()" class="h-full flex flex-col space-y-6">

    <!-- TAB 1: OVERVIEW -->
    <div x-show="currentTab === 'overview'" x-transition class="space-y-6">
        <!-- Top Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-700 via-emerald-800 to-slate-900 p-6 sm:p-8 border border-brand-500/30 shadow-2xl">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-emerald-200 border border-brand-400/30 mb-3">
                    🚀 Next-Gen WhatsApp Engine
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Welcome to <span class="text-brand-300">{{ $vendor->name }}</span>
                </h1>
                <p class="text-sm sm:text-base text-emerald-100/80 mt-2 leading-relaxed">
                    Direct Meta Graph API v20 connection with instant webhooks, broadcast dispatching, and AI-powered customer engagement.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <button @click="currentTab = 'chat'" class="px-4 py-2.5 rounded-xl bg-white text-slate-950 font-bold text-sm shadow hover:bg-slate-100 transition flex items-center gap-2">
                        <span>Open Live Inbox</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                    <button @click="currentTab = 'campaigns'" class="px-4 py-2.5 rounded-xl bg-emerald-900/60 text-white font-semibold text-sm border border-emerald-500/30 hover:bg-emerald-800/60 transition">
                        New Broadcast
                    </button>
                    <button @click="currentTab = 'templates'" class="px-4 py-2.5 rounded-xl bg-slate-900/70 text-slate-200 font-semibold text-sm border border-slate-700 hover:bg-slate-800 transition">
                        Sync Templates
                    </button>
                </div>
            </div>
            <!-- Decorative Background Graphic -->
            <div class="absolute -right-8 -bottom-12 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                    <span>Total Contacts</span>
                    <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">👥</span>
                </div>
                <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['total_contacts']) }}</div>
                <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="text-emerald-400 font-semibold">Active CRM Audience</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                    <span>Messages Sent</span>
                    <span class="p-2 rounded-lg bg-blue-500/10 text-blue-400">📤</span>
                </div>
                <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['total_messages_sent']) }}</div>
                <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="text-blue-400 font-semibold">Cloud API Outbound</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                    <span>Messages Inbound</span>
                    <span class="p-2 rounded-lg bg-purple-500/10 text-purple-400">📥</span>
                </div>
                <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['total_messages_received']) }}</div>
                <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="text-purple-400 font-semibold">Customer Replies</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                    <span>Approved Templates</span>
                    <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400">📋</span>
                </div>
                <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['approved_templates']) }}</div>
                <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="text-emerald-400 font-semibold">Meta Verified HSM</span>
                </div>
            </div>
        </div>

        <!-- Quick Integration Status Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                <h3 class="font-bold text-white text-base">Meta Webhook Handshake</h3>
                <p class="text-xs text-slate-400 mt-1">Status callback for real-time messages and delivery ticks.</p>
                <div class="mt-3 p-3 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 break-all border border-slate-800 flex items-center justify-between">
                    <span>{{ url('/api/webhook/whatsapp/' . $vendor->uuid) }}</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                <h3 class="font-bold text-white text-base">Shopify E-Commerce</h3>
                <p class="text-xs text-slate-400 mt-1">Automatic order confirmation & tracking dispatch.</p>
                <div class="mt-3 p-3 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 break-all border border-slate-800 flex items-center justify-between">
                    <span>{{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/shopify/orders-create') }}</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
                <h3 class="font-bold text-white text-base">WooCommerce Webhook</h3>
                <p class="text-xs text-slate-400 mt-1">Real-time status notifications for WordPress stores.</p>
                <div class="mt-3 p-3 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 break-all border border-slate-800 flex items-center justify-between">
                    <span>{{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/woocommerce/orders') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: LIVE CHAT (WHATSAPP WEB-STYLE INBOX) -->
    <div x-show="currentTab === 'chat'" x-transition class="flex-1 flex bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-2xl h-[calc(100vh-8.5rem)]">
        <!-- Left: Conversations Sidebar -->
        <div class="w-80 sm:w-96 border-r border-slate-800 flex flex-col bg-slate-950/60">
            <!-- Search & Filters -->
            <div class="p-3.5 border-b border-slate-800 space-y-2.5">
                <div class="relative">
                    <input type="text" x-model="chatSearch" @input.debounce.300ms="loadConversations()" placeholder="Search chats..." class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <!-- Filter tabs -->
                <div class="flex items-center gap-1 p-1 bg-slate-900/80 rounded-lg text-xs font-semibold">
                    <button @click="chatTab = 'all'; loadConversations()" :class="chatTab === 'all' ? 'bg-slate-800 text-white shadow' : 'text-slate-400 hover:text-slate-200'" class="flex-1 py-1 rounded text-center transition">All</button>
                    <button @click="chatTab = 'mine'; loadConversations()" :class="chatTab === 'mine' ? 'bg-slate-800 text-white shadow' : 'text-slate-400 hover:text-slate-200'" class="flex-1 py-1 rounded text-center transition">Mine</button>
                    <button @click="chatTab = 'unread'; loadConversations()" :class="chatTab === 'unread' ? 'bg-slate-800 text-white shadow' : 'text-slate-400 hover:text-slate-200'" class="flex-1 py-1 rounded text-center transition">Unread</button>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-800/40">
                <template x-for="conv in conversations" :key="conv.id">
                    <div @click="selectConversation(conv)" :class="activeConversation?.id === conv.id ? 'bg-slate-800/70 border-l-4 border-brand-500' : 'hover:bg-slate-900/40'" class="p-3.5 cursor-pointer transition flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-sm text-brand-400 flex-shrink-0">
                            <span x-text="(conv.first_name || conv.phone_number).charAt(0)"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-slate-200 truncate" x-text="conv.first_name ? conv.first_name + ' ' + (conv.last_name || '') : conv.phone_number"></span>
                                <span class="text-[10px] text-slate-500" x-text="conv.latest_message ? formatTime(conv.latest_message.created_at) : ''"></span>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-slate-400 truncate" x-text="conv.latest_message ? conv.latest_message.text : 'No messages yet'"></p>
                                <span x-show="conv.unread_count > 0" x-text="conv.unread_count" class="ml-2 px-1.5 py-0.2 rounded-full bg-brand-500 text-slate-950 font-bold text-[10px]"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right: Active Chat Window -->
        <div class="flex-1 flex flex-col bg-wa-chat">
            <template x-if="activeConversation">
                <div class="flex-1 flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="h-16 border-b border-slate-800/80 bg-slate-900/90 px-4 flex items-center justify-between flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-sm text-brand-400">
                                <span x-text="(activeConversation.first_name || activeConversation.phone_number).charAt(0)"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-white" x-text="activeConversation.first_name ? activeConversation.first_name + ' ' + (activeConversation.last_name || '') : activeConversation.phone_number"></h3>
                                <p class="text-xs text-slate-400" x-text="'+' + activeConversation.phone_number"></p>
                            </div>
                        </div>

                        <!-- Chat Controls -->
                        <div class="flex items-center gap-3">
                            <!-- Toggle Bot -->
                            <button @click="toggleBotForActiveContact()" :class="activeConversation.is_ai_bot_disabled ? 'bg-slate-800 text-slate-400' : 'bg-brand-500/20 text-brand-400 border border-brand-500/30'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" :class="activeConversation.is_ai_bot_disabled ? 'bg-slate-500' : 'bg-brand-400 animate-pulse'"></span>
                                <span x-text="activeConversation.is_ai_bot_disabled ? 'Bot Disabled' : 'AI Bot Active'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Messages Timeline Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3" id="messagesContainer">
                        <template x-for="msg in activeMessages" :key="msg.id">
                            <div :class="msg.direction === 'outbound' ? 'justify-end' : 'justify-start'" class="flex">
                                <div :class="msg.direction === 'outbound' ? 'bg-wa-bubble text-slate-100 rounded-tr-none' : 'bg-wa-incoming text-slate-200 rounded-tl-none'" class="max-w-md px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm shadow-md space-y-1">
                                    <p class="whitespace-pre-wrap leading-relaxed" x-text="msg.message_text"></p>
                                    <div class="flex items-center justify-end gap-1.5 text-[10px] text-slate-400">
                                        <span x-text="formatTime(msg.created_at)"></span>
                                        <template x-if="msg.direction === 'outbound'">
                                            <span>
                                                <template x-if="msg.status === 'read'"><span class="text-sky-400 font-bold">✓✓</span></template>
                                                <template x-if="msg.status === 'delivered'"><span class="text-slate-400">✓✓</span></template>
                                                <template x-if="msg.status === 'sent'"><span class="text-slate-400">✓</span></template>
                                                <template x-if="msg.status === 'failed'"><span class="text-rose-400">⚠️</span></template>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Message Input Composer -->
                    <div class="p-3 border-t border-slate-800 bg-slate-900/90 flex items-center gap-2">
                        <input type="text" x-model="newMessageText" @keydown.enter="sendTextMessage()" placeholder="Type a message..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                        <button @click="sendTextMessage()" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs sm:text-sm transition flex items-center gap-1.5">
                            <span>Send</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="!activeConversation">
                <div class="h-full flex flex-col items-center justify-center text-slate-500 p-6">
                    <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl mb-3">💬</div>
                    <h3 class="font-bold text-slate-300">Select a Conversation</h3>
                    <p class="text-xs text-slate-500 mt-1">Pick a contact from the left list to view WhatsApp messages.</p>
                </div>
            </template>
        </div>
    </div>

    <!-- TAB 3: CONTACTS & CRM -->
    <div x-show="currentTab === 'contacts'" x-transition class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-white">Contacts & Audience CRM</h2>
                <p class="text-xs text-slate-400">Manage contacts, custom groups, tags, and bulk Excel imports.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="/api/contacts/dummy-template" class="px-3.5 py-2 rounded-xl bg-slate-900 text-slate-300 border border-slate-700 text-xs font-semibold hover:bg-slate-800 transition">Download Excel Template</a>
                <a href="/api/contacts/export" class="px-3.5 py-2 rounded-xl bg-brand-600/20 text-brand-300 border border-brand-500/30 text-xs font-semibold hover:bg-brand-600/30 transition">Export Contacts (.xlsx)</a>
            </div>
        </div>

        <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
            <div class="p-3 border-b border-slate-800 flex items-center gap-3">
                <input type="text" x-model="contactSearch" @input.debounce.300ms="loadContacts()" placeholder="Search by name, phone or email..." class="bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500 w-72">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Groups</th>
                            <th class="px-4 py-3">Opt-out</th>
                            <th class="px-4 py-3">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <template x-for="c in contactsList" :key="c.id">
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3 font-bold text-slate-200" x-text="c.first_name ? c.first_name + ' ' + (c.last_name || '') : '-'"></td>
                                <td class="px-4 py-3 font-mono text-emerald-400" x-text="'+' + c.phone_number"></td>
                                <td class="px-4 py-3 text-slate-400" x-text="c.email || '-'"></td>
                                <td class="px-4 py-3">
                                    <template x-for="g in c.groups" :key="g.id">
                                        <span class="inline-block px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 mr-1 text-[10px]" x-text="g.title"></span>
                                    </template>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="c.is_opted_out ? 'text-rose-400' : 'text-emerald-400'" x-text="c.is_opted_out ? 'Yes' : 'No'"></span>
                                </td>
                                <td class="px-4 py-3 text-slate-500" x-text="formatDate(c.created_at)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: CAMPAIGNS -->
    <div x-show="currentTab === 'campaigns'" x-transition class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-white">Broadcast Campaigns</h2>
                <p class="text-xs text-slate-400">High-throughput bulk WhatsApp blasts with chunked queues and live progress metrics.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="camp in campaignsList" :key="camp.id">
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-white text-base" x-text="camp.title"></h3>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="'Template: ' + (camp.template ? camp.template.name : 'N/A')"></p>
                        </div>
                        <span :class="{
                            'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': camp.status === 'completed',
                            'bg-blue-500/20 text-blue-400 border-blue-500/30': camp.status === 'processing',
                            'bg-amber-500/20 text-amber-400 border-amber-500/30': camp.status === 'paused',
                            'bg-slate-800 text-slate-400 border-slate-700': camp.status === 'draft'
                        }" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border" x-text="camp.status"></span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Delivery Progress</span>
                            <span class="font-bold text-slate-200" x-text="camp.total_contacts > 0 ? Math.round(((camp.sent_count + camp.failed_count) / camp.total_contacts) * 100) + '%' : '0%'"></span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full bg-brand-500 rounded-full transition-all" :style="'width: ' + (camp.total_contacts > 0 ? ((camp.sent_count + camp.failed_count) / camp.total_contacts) * 100 : 0) + '%'"></div>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-4 gap-2 pt-2 border-t border-slate-800 text-center text-xs">
                        <div>
                            <div class="text-slate-500 text-[10px]">Total</div>
                            <div class="font-bold text-white" x-text="camp.total_contacts"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 text-[10px]">Sent</div>
                            <div class="font-bold text-emerald-400" x-text="camp.sent_count"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 text-[10px]">Delivered</div>
                            <div class="font-bold text-blue-400" x-text="camp.delivered_count"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 text-[10px]">Read</div>
                            <div class="font-bold text-purple-400" x-text="camp.read_count"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- TAB 5: TEMPLATES -->
    <div x-show="currentTab === 'templates'" x-transition class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-white">WhatsApp Templates (Meta HSM)</h2>
                <p class="text-xs text-slate-400">Meta Graph API verified message templates for 24-hour notification re-engagement.</p>
            </div>
            <button @click="syncTemplates()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-2">
                <span>Sync with Meta</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="tpl in templatesList" :key="tpl.id">
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-white text-base font-mono" x-text="tpl.name"></h3>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider" x-text="tpl.category"></span>
                        </div>
                        <span :class="tpl.status === 'APPROVED' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border" x-text="tpl.status"></span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300">
                        <template x-for="comp in tpl.components" :key="comp.type">
                            <div x-show="comp.type === 'BODY'" class="line-clamp-3 leading-relaxed" x-text="comp.text"></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- TAB 6: BOT AUTOMATIONS & SIMULATOR -->
    <div x-show="currentTab === 'automations'" x-transition class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-white">Bot Automations & AI Copilot</h2>
                <p class="text-xs text-slate-400">Configure auto-responders, priority triggers, and dry-run test incoming customer queries.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Bot Rules List -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="font-bold text-slate-200 text-sm">Active Auto-Reply Rules</h3>
                <div class="space-y-3">
                    <template x-for="rule in botRules" :key="rule.id">
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-white text-sm" x-text="rule.name"></span>
                                    <span class="px-2 py-0.5 rounded bg-brand-500/20 text-brand-400 border border-brand-500/30 text-[10px] uppercase font-bold" x-text="rule.trigger_type"></span>
                                    <span class="text-xs text-slate-500" x-text="'Priority: ' + rule.priority"></span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="kw in rule.trigger_keywords" :key="kw">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px]" x-text="kw"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right Col: Interactive Simulator Playground -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-brand-500/20 text-brand-400">🧪</span>
                    <div>
                        <h3 class="font-bold text-white text-sm">Bot Simulator</h3>
                        <p class="text-xs text-slate-400">Test incoming customer query</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <input type="text" x-model="simTestMessage" placeholder="e.g. What is the price?" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    <button @click="runSimulation()" class="w-full py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition">
                        Run Test Match
                    </button>
                </div>

                <!-- Sim Result -->
                <template x-if="simResult">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-semibold">Match Status:</span>
                            <span :class="simResult.matched ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold'" x-text="simResult.matched ? 'Trigger Matched' : 'No Match Found'"></span>
                        </div>
                        <template x-if="simResult.matched">
                            <div>
                                <div class="text-slate-500 text-[10px]">Triggered Rule:</div>
                                <div class="text-white font-bold" x-text="simResult.rule.name"></div>
                                <div class="mt-2 text-slate-500 text-[10px]">Bot Output:</div>
                                <div class="text-slate-300 font-mono p-2 rounded bg-slate-900 mt-1" x-text="simResult.simulated_reply.text"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TAB 7: E-COMMERCE & API -->
    <div x-show="currentTab === 'integrations'" x-transition class="space-y-6">
        <div>
            <h2 class="text-xl font-extrabold text-white">E-Commerce & Developer REST API v1</h2>
            <p class="text-xs text-slate-400">Integrate Shopify, WooCommerce, Zoho, Salesforce, or custom backends with VyapariChat.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Developer API Key -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-brand-500/20 text-brand-400 font-bold">🔑</span>
                    <div>
                        <h3 class="font-bold text-white text-base">Public REST API Key</h3>
                        <p class="text-xs text-slate-400">Authenticate requests via X-API-KEY header.</p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 font-mono text-xs text-emerald-400 break-all select-all">
                    {{ $vendor->getApiKey() ?: 'vyapari_live_' . md5($vendor->id . 'vyapari_secret') }}
                </div>

                <div class="space-y-2">
                    <div class="text-xs font-semibold text-slate-300">Quick cURL Example:</div>
                    <pre class="p-3 rounded-xl bg-slate-950 text-slate-300 text-[11px] font-mono overflow-x-auto border border-slate-800">
curl -X POST "{{ url('/api/v1/send-message') }}" \
  -H "X-API-KEY: {{ $vendor->getApiKey() ?: 'YOUR_API_KEY' }}" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "919876543210", "type": "text", "message": "Hello from VyapariChat API!"}'</pre>
                </div>
            </div>

            <!-- E-Commerce Webhooks Setup -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-blue-500/20 text-blue-400 font-bold">🛍️</span>
                    <div>
                        <h3 class="font-bold text-white text-base">Store Automation Webhooks</h3>
                        <p class="text-xs text-slate-400">Copy these URLs into your Shopify or WooCommerce admin settings.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="text-slate-400 font-semibold mb-1">Shopify: Order Created</div>
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 break-all">
                            {{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/shopify/orders-create') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-400 font-semibold mb-1">Shopify: Order Fulfilled / Tracking</div>
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 break-all">
                            {{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/shopify/orders-fulfill') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-400 font-semibold mb-1">WooCommerce: Order Webhook</div>
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 break-all">
                            {{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/woocommerce/orders') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 8: PLANS & BILLING -->
    <div x-show="currentTab === 'billing'" x-transition class="space-y-6">
        <div>
            <h2 class="text-xl font-extrabold text-white">Subscription & Usage Limits</h2>
            <p class="text-xs text-slate-400">Manage plan tier, feature gating, and payment methods.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <template x-for="plan in billingPlans" :key="plan.id">
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between space-y-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-white text-lg" x-text="plan.title"></h3>
                            <span x-show="plan.slug === 'pro-enterprise'" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-400 border border-brand-500/30">Popular</span>
                        </div>
                        <p class="text-xs text-slate-400" x-text="plan.description"></p>
                        <div class="text-3xl font-extrabold text-white">
                            ₹<span x-text="plan.price_monthly"></span><span class="text-xs text-slate-400 font-normal"> / month</span>
                        </div>
                        <ul class="text-xs text-slate-300 space-y-2 pt-3 border-t border-slate-800">
                            <li class="flex items-center gap-2">
                                <span class="text-brand-400">✓</span>
                                <span x-text="plan.max_contacts > 0 ? plan.max_contacts + ' Contacts Limit' : 'Unlimited Contacts'"></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-brand-400">✓</span>
                                <span x-text="plan.max_campaigns_per_month + ' Broadcasts / month'"></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-brand-400">✓</span>
                                <span x-text="plan.max_staff_members + ' Agent seats'"></span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span :class="plan.has_ai_bot ? 'text-brand-400' : 'text-slate-600'" x-text="plan.has_ai_bot ? '✓' : '✗'"></span>
                                <span :class="plan.has_ai_bot ? 'text-slate-300' : 'text-slate-500'">AI Copilot & Smart Bot</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span :class="plan.has_api_access ? 'text-brand-400' : 'text-slate-600'" x-text="plan.has_api_access ? '✓' : '✗'"></span>
                                <span :class="plan.has_api_access ? 'text-slate-300' : 'text-slate-500'">Public REST API Access</span>
                            </li>
                        </ul>
                    </div>

                    <button class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20">
                        Subscribe Now
                    </button>
                </div>
            </template>
        </div>
    </div>

</div>

<!-- Alpine Reactive State Script -->
<script>
function vyapariApp() {
    return {
        // Chat State
        chatTab: 'all',
        chatSearch: '',
        conversations: [],
        activeConversation: null,
        activeMessages: [],
        newMessageText: '',

        // Contacts State
        contactSearch: '',
        contactsList: [],

        // Campaigns State
        campaignsList: [],

        // Templates State
        templatesList: [],

        // Bot State
        botRules: [],
        simTestMessage: '',
        simResult: null,

        // Billing State
        billingPlans: [],

        init() {
            this.loadConversations();
            this.loadContacts();
            this.loadCampaigns();
            this.loadTemplates();
            this.loadBotRules();
            this.loadBillingPlans();
        },

        async loadConversations() {
            try {
                let res = await fetch(`/api/chat/conversations?tab=${this.chatTab}&search=${encodeURIComponent(this.chatSearch)}`);
                let json = await res.json();
                if (json.success) {
                    this.conversations = json.data.data || [];
                    if (!this.activeConversation && this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0]);
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        async selectConversation(conv) {
            this.activeConversation = conv;
            try {
                let res = await fetch(`/api/chat/conversations/${conv.id}/messages`);
                let json = await res.json();
                if (json.success) {
                    this.activeMessages = (json.data.data || []).reverse();
                    this.scrollToBottom();
                }
                // Mark unread as read
                if (conv.unread_count > 0) {
                    await fetch(`/api/chat/conversations/${conv.id}/mark-read`, { method: 'POST' });
                    conv.unread_count = 0;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async sendTextMessage() {
            if (!this.newMessageText.trim() || !this.activeConversation) return;
            let text = this.newMessageText.trim();
            this.newMessageText = '';

            try {
                let res = await fetch(`/api/chat/conversations/${this.activeConversation.id}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: 'text', message: text })
                });
                let json = await res.json();
                if (json.success) {
                    this.activeMessages.push(json.data);
                    this.scrollToBottom();
                }
            } catch (e) {
                console.error(e);
            }
        },

        async toggleBotForActiveContact() {
            if (!this.activeConversation) return;
            let newDisabled = !this.activeConversation.is_ai_bot_disabled;
            try {
                let res = await fetch(`/api/chat/conversations/${this.activeConversation.id}/toggle-bot`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ disabled: newDisabled })
                });
                let json = await res.json();
                if (json.success) {
                    this.activeConversation.is_ai_bot_disabled = newDisabled;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async loadContacts() {
            try {
                let res = await fetch(`/api/contacts?search=${encodeURIComponent(this.contactSearch)}`);
                let json = await res.json();
                if (json.success) {
                    this.contactsList = json.data.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        async loadCampaigns() {
            try {
                let res = await fetch('/api/campaigns');
                let json = await res.json();
                if (json.success) {
                    this.campaignsList = json.data.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        async loadTemplates() {
            try {
                let res = await fetch('/api/whatsapp/templates');
                let json = await res.json();
                if (json.success) {
                    this.templatesList = json.data.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        async syncTemplates() {
            try {
                let res = await fetch('/api/whatsapp/templates/sync', { method: 'POST' });
                let json = await res.json();
                alert(json.message || 'Templates synced.');
                this.loadTemplates();
            } catch (e) {
                alert('Sync failed.');
            }
        },

        async loadBotRules() {
            try {
                let res = await fetch('/api/bot-replies');
                let json = await res.json();
                if (json.success) {
                    this.botRules = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        async runSimulation() {
            if (!this.simTestMessage.trim()) return;
            try {
                let res = await fetch('/api/bot-replies/simulate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: this.simTestMessage })
                });
                let json = await res.json();
                if (json.success) {
                    this.simResult = json.data;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async loadBillingPlans() {
            try {
                let res = await fetch('/api/billing/plans');
                let json = await res.json();
                if (json.success) {
                    this.billingPlans = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                let container = document.getElementById('messagesContainer');
                if (container) container.scrollTop = container.scrollHeight;
            });
        },

        formatTime(dateStr) {
            if (!dateStr) return '';
            let d = new Date(dateStr);
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            let d = new Date(dateStr);
            return d.toLocaleDateString();
        }
    };
}
</script>
@endsection
