<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $ldapUser = \App\Ldap\User::where('mail', $this->input('email'))->first();

    if (!$ldapUser) {

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    $connection = new \LdapRecord\Connection(
        config('ldap.connections.default')
    );

    $authenticated = $connection->auth()->attempt(
        $ldapUser->getDn(),
        $this->input('password')
    );

    if (! $authenticated) {

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    $user = \App\Models\User::firstOrCreate(
        [
            'email' => $ldapUser->getFirstAttribute('mail'),
        ],
        [
            'name' => $ldapUser->getFirstAttribute('cn'),
            'password' => bcrypt(str()->random(32)),
        ]
    );

    Auth::login($user, $this->boolean('remember'));

    RateLimiter::clear($this->throttleKey());
}
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

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}