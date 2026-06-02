<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex" 
     style="height: calc(100vh - 180px); min-height: 500px;"
     x-data="chatContainer()" x-init="setupScroll()">
     
    <!-- Chat Sidebar -->
    <div class="w-64 border-r border-gray-200 bg-gray-50 flex flex-col">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-200 bg-white">
            <h3 class="font-bold text-gray-900 font-display">Discuss Hub</h3>
            <p class="text-xs text-gray-400">Internal company communications</p>
        </div>

        <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Channels Section -->
            <div>
                <div class="flex items-center justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    <span>Channels</span>
                </div>
                <div class="space-y-1">
                    @foreach(['#general', '#operations', '#hrd-payroll', '#finance-billing'] as $ch)
                        <button wire:click="selectChannel('{{ $ch }}')" 
                                class="w-full flex items-center px-3 py-2 text-sm rounded-md transition font-medium {{ ($activeTab === 'channel' && $activeChannel === $ch) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="mr-2 text-gray-400 font-bold font-mono">#</span>
                            {{ substr($ch, 1) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Direct Messages Section -->
            <div>
                <div class="flex items-center justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    <span>Direct Messages</span>
                </div>
                <div class="space-y-1">
                    @forelse($users as $u)
                        <button wire:click="selectDirectUser({{ $u->id }})" 
                                class="w-full flex items-center px-3 py-2 text-sm rounded-md transition font-medium {{ ($activeTab === 'direct' && $activeDirectUserId == $u->id) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="relative mr-2 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            {{ $u->name }}
                        </button>
                    @empty
                        <div class="text-xs text-gray-400 italic pl-3">No other active employees.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Active Chat Area -->
    <div class="flex-1 flex flex-col bg-slate-50">
        <!-- Active Chat Header -->
        <div class="h-16 px-6 border-b border-gray-200 bg-white flex items-center justify-between z-10">
            <div class="flex items-center">
                @if($activeTab === 'channel')
                    <span class="text-2xl font-bold font-mono text-gray-400 mr-2">#</span>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm font-display">{{ substr($activeChannel, 1) }}</h3>
                        <p class="text-[10px] text-gray-400">Broadcast channel to all sub-department employees.</p>
                    </div>
                @else
                    <span class="relative mr-2 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm font-display">{{ $activeDirectUser->name }}</h3>
                        <p class="text-[10px] text-gray-400">Secure Direct Employee Communication.</p>
                    </div>
                @endif
            </div>
            
            <div class="text-xs text-gray-400">
                🔒 Secured via ERP Intranet
            </div>
        </div>

        <!-- Messages stream -->
        <div id="chatStream" class="flex-1 overflow-y-auto p-6 space-y-4">
            @forelse($messages as $msg)
                @php
                    $isOwn = $msg->sender_id === Auth::id();
                @endphp
                <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-md space-y-0.5">
                        <!-- Message Author Header -->
                        @if(!$isOwn)
                            <div class="text-[10px] font-semibold text-gray-500 pl-1">
                                {{ $msg->sender->name }}
                            </div>
                        @endif

                        <!-- Message Content Bubble -->
                        <div class="px-4 py-2.5 rounded-lg shadow-sm text-sm {{ $isOwn ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none' }}">
                            <p class="whitespace-pre-line">{{ $msg->message }}</p>
                        </div>

                        <!-- Message Timestamp Footer -->
                        <div class="text-[9px] text-gray-400 font-mono {{ $isOwn ? 'text-right pr-1' : 'pl-1' }}">
                            {{ $msg->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center text-gray-400 text-sm italic">
                    No communications in this channel yet. Say Hello!
                </div>
            @endforelse
        </div>

        <!-- Chat Input Bar -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input wire:model="messageText" type="text" 
                       placeholder="Type your secure message here..." 
                       class="flex-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm px-4 py-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function chatContainer() {
        return {
            setupScroll() {
                // Auto scroll to bottom upon load
                this.scrollToBottom();

                // Listen to Livewire's dispatch event for scrolls
                window.addEventListener('chat-updated', () => {
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                });
            },
            scrollToBottom() {
                const chatStream = document.getElementById('chatStream');
                if (chatStream) {
                    chatStream.scrollTop = chatStream.scrollHeight;
                }
            }
        }
    }
</script>
