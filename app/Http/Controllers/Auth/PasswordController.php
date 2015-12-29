<?php

namespace App\Http\Controllers\Auth;

use Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $subject = "Сброс пароля";
    protected $redirectTo = '/game';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function postReset()
    {
        $data = Request::all();
        $user = User::where('email', '=', $data['email'])->first();
        if ($user !== null) {
            if ($data['password'] === $data['password_confirmation']) {
                $user->update(['password' => bcrypt($data['password'])]);
                Auth::login($user);
                return redirect('/game');
            }
            return view('auth/reset', ['error_message' => 'Пароли не совпадают.']);
        }
        return view('auth/reset', ['error_message' => 'Пользователь не найден.']);
    }
}
