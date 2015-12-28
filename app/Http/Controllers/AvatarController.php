<?php

namespace App\Http\Controllers;

use Auth;
use Request;
use Input;
use App\Models\Hair;
use App\Models\Mustache;
use App\Models\Amulet;
use App\Models\Flag;
use App\Models\Avatar;
use Illuminate\Routing\Controller;

class AvatarController extends Controller
{
    public function postPartial(Request $request)
    {
        $position = Input::get('position');
        $has_next = Input::get('has_next');
        $img_id = Input::get('img_id');

        $img_partial = null;
        if ($position === 'top')
        {
            $img_partial = ($has_next === 'true') ? Hair::find(((int)$img_id)+1) : Hair::find(((int)$img_id)-1);
        }
        else if ($position === 'middle')
        {
            $img_partial = ($has_next === 'true') ? Mustache::find(((int)$img_id)+1) : Mustache::find(((int)$img_id)-1);
        }
        else if ($position === 'bottom')
        {
            $img_partial = ($has_next === 'true') ? Amulet::find(((int)$img_id)+1) : Amulet::find(((int)$img_id)-1);
        }
        else if ($position === 'flag')
        {
            $img_partial = ($has_next === 'true') ? Flag::find(((int)$img_id)+1) : Flag::find(((int)$img_id)-1);
        }
        return response()->json(['url' => $img_partial->image_url, 'id' => $img_partial->id]);
    }
}