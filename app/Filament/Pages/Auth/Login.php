<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public ?string $captchaToken = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                View::make('components.turnstile'),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $this->validateCaptcha();

        return parent::authenticate();
    }

    protected function validateCaptcha(): void
    {
        $secretKey = config('services.turnstile.secret_key');

        if (empty($secretKey)) {
            return;
        }

        if (empty($this->captchaToken)) {
            throw ValidationException::withMessages([
                'captchaToken' => 'Silakan selesaikan verifikasi CAPTCHA terlebih dahulu.',
            ]);
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secretKey,
            'response' => $this->captchaToken,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->successful() || ! ($response->json('success') ?? false)) {
            throw ValidationException::withMessages([
                'captchaToken' => 'Verifikasi CAPTCHA gagal. Silakan coba lagi.',
            ]);
        }
    }
}
