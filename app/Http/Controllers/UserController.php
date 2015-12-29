<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use Auth;
use Request;
use Input;
use App\Models\CommentBlock;
use App\Models\User;
use App\Models\Hair;
use App\Models\Mustache;
use App\Models\Amulet;
use App\Models\Flag;
use Illuminate\Routing\Controller;
#use Illuminate\Foundation\Validation\ValidatesRequests;
#use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    public function getProfile($id)
    {
        $user = User::find($id);
        $page = Input::get('page');
        $page = isset($page) ? Input::get('page') : 1;
        $comment_block_id = $user->comment_block_id;
        $block = CommentBlock::find($comment_block_id);
        $comments = $block->getPage($page);
        $page_count = $block->getPageCount();
        $avatar = Avatar::find($user->avatar_id);
        return view('user/profile', [
            'user' => $user,
            'block' => $block,
            'comments' => $comments,
            'page_count' => $page_count,
            'page' => $page,
            'avatar' => $avatar,
            'hair_url' => Hair::find($avatar->hair_id)->image_url,
            'mustache_url' => Mustache::find($avatar->mustache_id)->image_url,
            'amulet_url' => Amulet::find($avatar->amulet_id)->image_url,
            'flag_url' => Flag::find($avatar->flag_id)->image_url,
        ]);
    }

    public function addComment()
    {
        $user = Auth::user();
        $comments_block_id = Input::get('comment_block_id');
        $profile_id = Input::get('profile_id');
        $text = Input::get('text');
        $parent_comment_id = Input::get('parent_comment_id');
        CommentBlock::find($comments_block_id)->addComment(
            $user->id,
            $text,
            $parent_comment_id == '' ? null : $parent_comment_id
        );
        return redirect('user/profile/'.$profile_id.'?page=1');
    }

    public function postUpdate()
    {
        $data = Request::all();

        $user = Auth::user();

        $avatar = Avatar::find($user->avatar_id);
        $avatar->update(['mustache_id' => $data['mustache_id'],
                         'amulet_id' => $data['amulet_id'],
                         'hair_id' => $data['hair_id'],
                         'flag_id' => $data['flag_id']]);

        $page = Input::get('page');
        $page = isset($page) ? Input::get('page') : 1;
        $comment_block_id = $user->comment_block_id;
        $block = CommentBlock::find($comment_block_id);
        $comments = $block->getPage($page);
        $page_count = $block->getPageCount();

        $message = 'Не удалось обновить профиль';
        $is_error = true;

        if ($user->update(['name' => $data['name'], 'castle_name' => $data['castle_name']])) {
            $is_error = false;
            $message = 'Профиль успешно обновлен';
        }

        return view('user/profile', ['user' => $user,
                                     'block' => $block,
                                     'comments' => $comments,
                                     'page_count' => $page_count,
                                     'page' => $page,
                                     'avatar' => $avatar,
                                     'hair_url' => Hair::find($avatar->hair_id)->image_url,
                                     'mustache_url' => Mustache::find($avatar->mustache_id)->image_url,
                                     'amulet_url' => Amulet::find($avatar->amulet_id)->image_url,
                                     'flag_url' => Flag::find($avatar->flag_id)->image_url,
                                     'is_error' => $is_error,
                                     'message' => $message]);
    }
}