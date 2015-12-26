<?php

namespace App\Http\Controllers;

use Auth;
use Request;
use App\Models\User;
use Illuminate\Routing\Controller;
#use Illuminate\Foundation\Validation\ValidatesRequests;
#use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    public function getProfile()
    {
        return view('user/profile', ['user' => Auth::user()]);
    }

    public function postUpdate()
    {
        $data = Request::all();

        $user = Auth::user();

        if ($user->update(['name' => $data['name'], 'castle_name' => $data['castle_name']])) {
            return view('user/profile', ['user' => $user, 'notice_message' => 'Профиль успешно обновлен']);
        }

        return view('user/profile', ['user' => $user, 'error_message' => 'Не удалось обновить профиль']);
    }
}