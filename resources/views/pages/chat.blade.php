@extends('layouts.app')

@section('content')
<div x-data="vyapariChatApp()" x-init="init()" class="flex-1 flex bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-2xl h-[calc(100vh-7.5rem)]">
    <!-- Left: Conversations Sidebar -->
    <div class="w-80 sm:w-96 border-r border-slate-800 flex flex-col bg-slate-950/60 flex-shrink-0">
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
                            <p class="text-xs text-slate-400 truncate" x-text="conv.latest_message ? (conv.latest_message.message_text || conv.latest_message.text) : 'No messages yet'"></p>
                            <span x-show="conv.unread_count > 0" x-text="conv.unread_count" class="ml-2 px-1.5 py-0.2 rounded-full bg-brand-500 text-slate-950 font-bold text-[10px]"></span>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="conversations.length === 0" class="p-8 text-center text-slate-500 text-xs">
                No active conversations found.
            </div>
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

<script>
function vyapariChatApp() {
    return {
        chatTab: 'all',
        chatSearch: '',
        conversations: [],
        activeConversation: null,
        activeMessages: [],
        newMessageText: '',

        init() {
            this.loadConversations();
            // Optional auto poll every 5s
            setInterval(() => {
                if (this.activeConversation) {
                    this.refreshActiveMessages();
                }
            }, 5000);
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
                if (conv.unread_count > 0) {
                    await fetch(`/api/chat/conversations/${conv.id}/mark-read`, { method: 'POST' });
                    conv.unread_count = 0;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async refreshActiveMessages() {
            if (!this.activeConversation) return;
            try {
                let res = await fetch(`/api/chat/conversations/${this.activeConversation.id}/messages`);
                let json = await res.json();
                if (json.success) {
                    this.activeMessages = (json.data.data || []).reverse();
                }
            } catch (e) {}
        },

        async sendTextMessage() {
            if (!this.newMessageText.trim() || !this.activeConversation) return;
            let text = this.newMessageText.trim();
            this.newMessageText = '';

            try {
                let res = await fetch(`/api/chat/conversations/${this.activeConversation.id}/send`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
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
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
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
        }
    };
}
</script>
@endsection
