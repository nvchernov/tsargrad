<?php

namespace App\Http\Controllers;

use Auth;
use Request;
use Input;
use App\Models\CommentBlock;
use App\Models\User;
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
        return view('user/profile', [
            'user' => $user,
            'block' => $block,
            'comments' => $comments,
            'page_count' => $page_count,
            'page' => $page
        ]);
    }

    public function addComment()
    {
        $user = Auth::user();
        $comments_block_id = Input::get('comment_block_id');
        $text = Input::get('text');
        $parent_comment_id = Input::get('parent_comment_id');
        CommentBlock::find($comments_block_id)->addComment(
            $user->id,
            $text,
            $parent_comment_id == '' ? null : $parent_comment_id
        );
        return redirect('user/profile?page=1');
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