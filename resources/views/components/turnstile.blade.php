<div
    x-data="{
        init() {
            this.renderTurnstile();
        },
        renderTurnstile() {
            if (window.turnstile && this.$refs.turnstileContainer) {
                try {
                    this.$refs.turnstileContainer.innerHTML = '';
                    turnstile.render(this.$refs.turnstileContainer, {
                        sitekey: '{{ config('services.turnstile.site_key') }}',
                        callback: (token) => {
                            $wire.set('captchaToken', token);
                        },
                        'expired-callback': () => {
                            $wire.set('captchaToken', '');
                        },
                        'error-callback': () => {
                            $wire.set('captchaToken', '');
                        }
                    });
                } catch (e) {
                    console.error('Turnstile render error:', e);
                }
            } else {
                setTimeout(() => this.renderTurnstile(), 150);
            }
        }
    }"
    class="flex flex-col items-center justify-center my-3"
>
    <div x-ref="turnstileContainer" wire:ignore></div>
    
    @error('captchaToken')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400 text-center font-medium">{{ $message }}</p>
    @enderror

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</div>
