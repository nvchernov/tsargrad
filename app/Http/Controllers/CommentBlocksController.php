<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Models\CommentBlock;
use Illuminate\Http\Request;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommentBlocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function comments($id,$page)
    {
        $block = CommentBlock::find($id);
        $comments = $block->getPage($page);
        $page_count = $block->getPageCount();
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/comments/comments_block.php');
    }

    public function add()
    {
        $user = Auth::user();
        $comments_block_id = Input::get('comment_block_id');
        $text = Input::get('text');
        $parent_comment_id = Input::get('$parent_comment_id');
        CommentBlock::find($comments_block_id)->addComment($user->id, $text, $parent_comment_id);
        return redirect('comments/'.$comments_block_id.'/1');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
