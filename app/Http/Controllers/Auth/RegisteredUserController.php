<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_whatsapp' => ['required', 'string', 'max:25', function (string $attribute, mixed $value, \Closure $fail) {
                // Cek angka setelah dinormalisasi: 08xx / +62 8xx / 62-8xx, 9–13 digit setelah 62.
                if (! preg_match('/^628\d{7,12}$/', (string) Phone::normalize((string) $value))) {
                    $fail('Format nomor WhatsApp tidak valid (contoh: 081234567890).');
                }
            }],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $noWhatsapp = Phone::normalize($request->no_whatsapp);

        if (User::where('no_whatsapp', $noWhatsapp)->exists()) {
            throw ValidationException::withMessages([
                'no_whatsapp' => 'Nomor WhatsApp sudah terdaftar.',
            ]);
        }

        // Registrasi publik selalu menjadi penyewa (role default di model User).
        $user = DB::transaction(function () use ($request, $noWhatsapp) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'no_whatsapp' => $noWhatsapp,
                'password' => Hash::make($request->password),
            ]);

            $user->penyewa()->create([
                'nama' => $request->name,
                'no_whatsapp' => $noWhatsapp,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
