<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-teal to-emerald flex items-center justify-center shadow-sm">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 leading-none">Swiftie</h2>
                    <p class="text-xs text-emerald dark:text-emerald-light mt-0.5">Online · Powered by Groq</p>
                </div>
            </div>
            <button id="clear-btn"
                    class="text-xs text-slate dark:text-slate-light hover:text-red-500 dark:hover:text-red-400 transition-colors flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
                Clear history
            </button>
        </div>
    </x-slot>

    <div class="flex flex-col h-[calc(100vh-4rem)]">

        {{-- Quick action chips --}}
        <div id="chips" class="flex flex-wrap gap-2 px-4 sm:px-6 lg:px-8 py-3 border-b border-gray-100 dark:border-gray-700
                               bg-white/60 dark:bg-surface-dark-card/60 backdrop-blur">
            @foreach ([
                ['Track a shipment',       'Track shipment MS-'],
                ['Get a shipping quote',   'I need a quote for a shipment from '],
                ['Urgent pickup help',     'I need to book an urgent pickup for a frozen specimen from '],
                ['Operations summary',     'Give me a summary of current operations'],
                ['List my shipments',      'List my recent shipments'],
                ['Cold chain guidelines',  'What are the cold chain compliance requirements for frozen blood samples?'],
            ] as [$label, $prompt])
                <button data-prompt="{{ $prompt }}"
                        class="chip rounded-full border border-teal/30 dark:border-teal/40 text-xs px-3 py-1.5
                               text-teal dark:text-teal-light bg-teal/5 dark:bg-teal/10
                               hover:bg-teal hover:text-white dark:hover:bg-teal transition-colors whitespace-nowrap">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Message thread --}}
        <div id="messages"
             class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4 scroll-smooth"
             style="background: radial-gradient(ellipse at top, rgba(22,151,169,0.04) 0%, transparent 70%)">

            {{-- Welcome bubble --}}
            <div class="flex items-start gap-3 max-w-2xl">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-teal to-emerald flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                    </svg>
                </div>
                <div class="rounded-2xl rounded-tl-sm px-4 py-3 bg-white dark:bg-surface-dark-card shadow-sm
                            border border-gray-100 dark:border-gray-700/50 text-sm text-gray-700 dark:text-gray-300">
                    Hello, <strong>{{ auth()->user()?->name ?? 'there' }}</strong>! I'm <strong>Swiftie</strong>, your MedSwift logistics assistant. I can track shipments, generate quotes, explain cold-chain requirements, and more.<br>
                    <span class="text-xs text-slate dark:text-slate-light mt-1 block">Use the quick actions above or type your question below.</span>
                </div>
            </div>

        </div>

        {{-- Input bar --}}
        <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-surface-dark-card px-4 sm:px-6 lg:px-8 py-4">
            <form id="chat-form" class="flex items-end gap-3">
                <div class="flex-1 relative">
                    <textarea id="user-input"
                              rows="1"
                              placeholder="Ask Swiftie anything…"
                              class="w-full resize-none rounded-xl border border-gray-200 dark:border-gray-600
                                     bg-gray-50 dark:bg-surface-dark text-gray-800 dark:text-gray-100
                                     placeholder-slate dark:placeholder-slate-light
                                     focus:outline-none focus:ring-2 focus:ring-teal/40 focus:border-teal
                                     dark:focus:border-teal-light text-sm px-4 py-3 pr-12
                                     transition-all duration-150 overflow-hidden"
                              style="max-height: 8rem"
                    ></textarea>
                </div>
                <button type="submit" id="send-btn"
                        class="h-11 w-11 rounded-xl bg-gradient-to-br from-teal to-emerald text-white
                               flex items-center justify-center shadow-sm
                               hover:from-teal-dark hover:to-emerald-dark transition-all
                               disabled:opacity-50 disabled:cursor-not-allowed shrink-0">
                    <svg id="send-icon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                    </svg>
                    <svg id="loading-icon" class="h-5 w-5 hidden animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                </button>
            </form>
            <p class="mt-2 text-xs text-center text-slate dark:text-slate-light">
                Swiftie may make mistakes. Always verify critical medical logistics details.
            </p>
        </div>

    </div>

    @push('scripts')
    {{-- marked.js for markdown rendering --}}
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
    const messagesEl  = document.getElementById('messages');
    const form        = document.getElementById('chat-form');
    const input       = document.getElementById('user-input');
    const sendBtn     = document.getElementById('send-btn');
    const sendIcon    = document.getElementById('send-icon');
    const loadingIcon = document.getElementById('loading-icon');
    const clearBtn    = document.getElementById('clear-btn');

    // Configure marked
    marked.setOptions({ breaks: true, gfm: true });

    // Auto-resize textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 128) + 'px';
    });

    // Submit on Enter (Shift+Enter = newline)
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && ! e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    // Quick action chips
    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', () => {
            input.value = chip.dataset.prompt;
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 128) + 'px';
            input.focus();
        });
    });

    // Clear history
    clearBtn.addEventListener('click', async () => {
        if (! confirm('Clear conversation history?')) return;
        await fetch('{{ route("ai.clear") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        });
        // Remove all bubbles except the welcome one
        const bubbles = messagesEl.querySelectorAll('[data-bubble]');
        bubbles.forEach(b => b.remove());
    });

    // Send message
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = input.value.trim();
        if (! message) return;

        // Append user bubble
        appendBubble('user', message);
        input.value = '';
        input.style.height = 'auto';

        // Show loading state
        setLoading(true);
        const thinkingEl = appendThinking();

        try {
            const res = await fetch('{{ route("ai.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message }),
            });

            const data = await res.json();
            thinkingEl.remove();

            if (data.error) {
                appendBubble('ai', '⚠️ ' + data.error);
            } else {
                appendBubble('ai', data.reply, true);
            }
        } catch (err) {
            thinkingEl.remove();
            appendBubble('ai', '⚠️ Network error. Please check your connection and try again.');
        } finally {
            setLoading(false);
        }
    });

    function appendBubble(role, text, isMarkdown = false) {
        const isUser = role === 'user';
        const wrapper = document.createElement('div');
        wrapper.setAttribute('data-bubble', role);
        wrapper.className = `flex items-start gap-3 ${isUser ? 'flex-row-reverse max-w-2xl ml-auto' : 'max-w-2xl'}`;

        const avatar = document.createElement('div');
        if (isUser) {
            avatar.className = 'h-8 w-8 rounded-full bg-teal/20 dark:bg-teal/30 flex items-center justify-center shrink-0 text-teal dark:text-teal-light text-xs font-bold';
            avatar.textContent = '{{ substr(auth()->user()?->name ?? 'G', 0, 1) }}';
        } else {
            avatar.className = 'h-8 w-8 rounded-lg bg-gradient-to-br from-teal to-emerald flex items-center justify-center shrink-0 shadow-sm';
            avatar.innerHTML = `<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>`;
        }

        const bubble = document.createElement('div');
        if (isUser) {
            bubble.className = 'rounded-2xl rounded-tr-sm px-4 py-3 bg-gradient-to-br from-teal to-teal-dark text-white text-sm shadow-sm max-w-full';
            bubble.textContent = text;
        } else {
            bubble.className = 'rounded-2xl rounded-tl-sm px-4 py-3 bg-white dark:bg-surface-dark-card shadow-sm border border-gray-100 dark:border-gray-700/50 text-sm text-gray-700 dark:text-gray-200 prose prose-sm dark:prose-invert max-w-full prose-table:text-xs prose-a:text-teal dark:prose-a:text-teal-light';
            bubble.innerHTML = isMarkdown ? marked.parse(text) : escapeHtml(text);
        }

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        messagesEl.appendChild(wrapper);
        scrollToBottom();
        return wrapper;
    }

    function appendThinking() {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-3 max-w-2xl';

        const avatar = document.createElement('div');
        avatar.className = 'h-8 w-8 rounded-lg bg-gradient-to-br from-teal to-emerald flex items-center justify-center shrink-0 shadow-sm';
        avatar.innerHTML = `<svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>`;

        const bubble = document.createElement('div');
        bubble.className = 'rounded-2xl rounded-tl-sm px-4 py-3 bg-white dark:bg-surface-dark-card shadow-sm border border-gray-100 dark:border-gray-700/50 flex items-center gap-1.5';
        bubble.innerHTML = `
            <span class="h-2 w-2 rounded-full bg-teal dark:bg-teal-light animate-bounce" style="animation-delay: 0ms"></span>
            <span class="h-2 w-2 rounded-full bg-teal dark:bg-teal-light animate-bounce" style="animation-delay: 150ms"></span>
            <span class="h-2 w-2 rounded-full bg-teal dark:bg-teal-light animate-bounce" style="animation-delay: 300ms"></span>
        `;

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        messagesEl.appendChild(wrapper);
        scrollToBottom();
        return wrapper;
    }

    function setLoading(state) {
        sendBtn.disabled = state;
        sendIcon.classList.toggle('hidden', state);
        loadingIcon.classList.toggle('hidden', ! state);
        input.disabled = state;
    }

    function scrollToBottom() {
        messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior: 'smooth' });
    }

    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
    }
    </script>
    @endpush
</x-app-layout>
