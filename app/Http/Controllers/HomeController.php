<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
#use Illuminate\Foundation\Validation\ValidatesRequests;
#use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeController extends Controller
{
    public function home()
	{
		return view('home');
	}
}
