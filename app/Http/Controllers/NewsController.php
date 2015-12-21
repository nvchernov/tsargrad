<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $news = News::all();
        return $news;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $entry = News::find($id);
        return $entry;
    }

}
