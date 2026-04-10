<?php
$chatbotEndpoint = app_url('/api/chat.php');
$chatbotUser = $_SESSION['full_name'] ?? ($_SESSION['username'] ?? 'Foydalanuvchi');
$chatbotRole = is_admin() ? 'admin' : 'user';
?>
<div
    id="ai-chatbot"
    data-chatbot
    data-endpoint="<?= h($chatbotEndpoint) ?>"
    data-role="<?= h($chatbotRole) ?>"
    class="fixed bottom-5 right-5 z-[70] sm:bottom-6 sm:right-6"
>
    <button
        type="button"
        data-chatbot-toggle
        class="group flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-[0_18px_45px_rgba(5,150,105,0.35)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_55px_rgba(5,150,105,0.42)] focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:h-15 sm:w-15"
        aria-label="AI Chatbot"
    >
        <i class="fas fa-robot text-xl transition group-hover:scale-110"></i>
    </button>

    <div
        data-chatbot-panel
        class="pointer-events-none absolute bottom-0 right-0 w-[min(380px,calc(100vw-1.5rem))] max-w-[calc(100vw-1.5rem)] translate-y-10 scale-95 opacity-0 transition duration-300 sm:w-[380px] sm:max-w-[380px]"
    >
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.18)]">
            <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 px-5 py-4 text-white">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-xs uppercase tracking-[0.22em] text-emerald-200">
                            <i class="fas fa-sparkles text-[11px]"></i> AI Assistant
                        </div>
                        <h3 class="mt-1 text-base font-semibold">AI Bot</h3>
                        <p class="mt-1 text-xs text-white/65 truncate"><?= h($chatbotUser) ?> uchun yordamchi</p>
                    </div>
                    <button
                        type="button"
                        data-chatbot-close
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-white/80 transition hover:bg-white/15 hover:text-white"
                        aria-label="Yopish"
                    >
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div data-chatbot-messages class="h-[300px] overflow-y-auto bg-slate-50 px-3 py-4 sm:h-[400px] sm:px-4">
                <div class="mb-4 flex">
                    <div class="max-w-[88%] rounded-2xl rounded-tl-md bg-white px-4 py-3 text-sm text-slate-700 shadow-sm ring-1 ring-slate-200">
                        Salom, men AI yordamchiman. Tizim, testlar yoki materiallar bo‘yicha savol yozishingiz mumkin.
                    </div>
                </div>
            </div>

            <form data-chatbot-form class="border-t border-slate-100 bg-white p-3 sm:p-4">
                <div class="flex items-end gap-2 sm:gap-3">
                    <textarea
                        data-chatbot-input
                        rows="1"
                        maxlength="1000"
                        placeholder="Savolingizni yozing..."
                        class="max-h-32 min-h-[52px] flex-1 resize-none rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-300 sm:px-4"
                    ></textarea>
                    <button
                        type="submit"
                        data-chatbot-send
                        class="inline-flex h-[52px] min-w-[52px] items-center justify-center rounded-2xl bg-emerald-600 px-3 text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 disabled:cursor-not-allowed disabled:opacity-60 sm:px-4"
                    >
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
                <p class="mt-2 text-xs text-slate-400">AI xato qilishi mumkin.</p>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const root = document.querySelector('[data-chatbot]');
    if (!root) return;

    const toggle = root.querySelector('[data-chatbot-toggle]');
    const closeBtn = root.querySelector('[data-chatbot-close]');
    const panel = root.querySelector('[data-chatbot-panel]');
    const messages = root.querySelector('[data-chatbot-messages]');
    const form = root.querySelector('[data-chatbot-form]');
    const input = root.querySelector('[data-chatbot-input]');
    const sendButton = root.querySelector('[data-chatbot-send]');
    const endpoint = root.dataset.endpoint;
    let isOpen = false;
    let isSending = false;

    const setOpen = (next) => {
        isOpen = next;
        toggle.classList.toggle('pointer-events-none', next);
        toggle.classList.toggle('opacity-0', next);
        toggle.classList.toggle('scale-75', next);
        toggle.classList.toggle('translate-y-3', next);
        panel.classList.toggle('pointer-events-none', !next);
        panel.classList.toggle('opacity-0', !next);
        panel.classList.toggle('translate-y-10', !next);
        panel.classList.toggle('scale-95', !next);
        if (next) {
            input.focus();
        }
    };

    const appendMessage = (text, type = 'bot') => {
        const wrapper = document.createElement('div');
        wrapper.className = `mb-4 flex ${type === 'user' ? 'justify-end' : ''}`;

        const bubble = document.createElement('div');
        bubble.className = type === 'user'
            ? 'max-w-[88%] rounded-2xl rounded-br-md bg-emerald-600 px-4 py-3 text-sm text-white shadow-sm'
            : 'max-w-[88%] rounded-2xl rounded-tl-md bg-white px-4 py-3 text-sm text-slate-700 shadow-sm ring-1 ring-slate-200';
        bubble.textContent = text;

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        messages.scrollTop = messages.scrollHeight;
        return wrapper;
    };

    const autoResize = () => {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 128)}px`;
    };

    toggle.addEventListener('click', () => setOpen(!isOpen));
    closeBtn.addEventListener('click', () => setOpen(false));
    input.addEventListener('input', autoResize);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (isSending) return;

        const message = input.value.trim();
        if (!message) return;

        appendMessage(message, 'user');
        input.value = '';
        autoResize();
        isSending = true;
        sendButton.disabled = true;
        const typingNode = appendMessage('Yozmoqda...', 'bot');

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            typingNode.remove();
            appendMessage(data.reply || 'AI tizim hali ishga tushirilmagan', 'bot');
        } catch (error) {
            typingNode.remove();
            appendMessage('AI javobida vaqtinchalik uzilish bor. Keyinroq yana urinib ko‘ring.', 'bot');
        } finally {
            isSending = false;
            sendButton.disabled = false;
            if (!isOpen) setOpen(true);
        }
    });
})();
</script>
