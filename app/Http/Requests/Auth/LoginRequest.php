<?php

namespace App\Http\Requests\Auth;

use App\Support\Phone;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Bisa diisi email atau nomor WhatsApp.
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->credentials();

        if ($credentials === null || ! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Kredensial login: kolom email kalau isinya email, selain itu nomor WhatsApp.
     *
     * Null kalau isian bukan email maupun nomor yang valid (mencegah
     * where no_whatsapp IS NULL mencocokkan akun tanpa nomor WA).
     *
     * @return array<string, string>|null
     */
    protected function credentials(): ?array
    {
        $login = $this->string('email')->trim()->value();

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return ['email' => Str::lower($login), 'password' => $this->string('password')->value()];
        }

        $wa = Phone::normalize($login);

        return $wa === null ? null : ['no_whatsapp' => $wa, 'password' => $this->string('password')->value()];
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Pakai identitas yang sudah dinormalisasi, supaya "0811-..." dan
        // "+62811..." (akun yang sama) berbagi jatah percobaan yang sama.
        $login = $this->string('email')->trim()->value();

        $id = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? Str::lower($login)
            : (Phone::normalize($login) ?? 'invalid');

        return Str::transliterate($id.'|'.$this->ip());
    }
}
