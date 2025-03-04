<?php

namespace App\Http\Requests\Auth;

use Hash;
use Illuminate\Auth\Events\Lockout;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emailOrNIS' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('emailOrNIS', 'password');

        // Cari user berdasarkan email, nis
        $user = $this->findUser($credentials);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'emailOrNIS' => trans('auth.failed'),
            ])->status(422);
        }

        if (!$user->hasAnyRole(['admin', 'santri'])) {
            RateLimiter::hit($this->throttleKey());
            session()->flash('status', 'Error: Akun tidak komplit');
            session()->flash('status_type', 'error');
            return;
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Find the user based on the provided credentials.
     *
     * @param array $credentials
     * @return \App\Models\User|null
     */
    protected function findUser(array $credentials)
    {
        $identifier = $credentials['emailOrNIS'] ?? null;

        if (!$identifier) {
            return null;
        }

        // Jika format email valid
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return \App\Models\User::where('email', $identifier)->first();
        }

        // Jika format NIS (hanya angka, panjang 6 digit)
        if (preg_match('/^\d{6}$/', $identifier)) {
            $santri = \App\Models\Santri::where('nis', $identifier)->first();
            return $santri ? \App\Models\User::find($santri->user_id) : null;
        }

        return null;
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'emailOrNIS' => trans('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->string('emailOrNIS')) . '|' . $this->ip());
    }
}
