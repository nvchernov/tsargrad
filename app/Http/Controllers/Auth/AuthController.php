<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Request;
use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function getLogin()
    {
        if (Auth::check())
        {
            return redirect('home');
        }
        return view('auth/login');
    }

    public function postLogin()
    {
        $data = Request::all();

        $remember =isset($data['remember']) ? $data['remember'] : false;
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember))
        {
            return redirect('home');
        }
        return view('auth/login', ['error_message' => 'Неверный email или пароль']);
    }

    public function getLogout()
    {
        Auth::logout();
        return view('auth/login');
    }

    /*public function getRegister()
    {
        return redirect('auth/register');
    }*/

    public function postRegister()
    {
        $data = Request::all();
        if ($data['password'] === $data['password_confirmation'])
        {
            if ($this->create($data) !== null)
            {
                return redirect('home');
            }
        }
        return redirect('auth/register');
    }
}
