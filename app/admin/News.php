<?php

Admin::model(\App\Models\News::class)->title('News')->with()->filters(function ()
{

})->columns(function ()
{
	Column::string('title', 'Title');
	Column::date('date', 'Date')->format('medium', 'none');
	Column::string('published', 'Published');
//	Column::string('text', 'Text');
	Column::image('photo', 'Photo')->sortable(false);
})->form(function ()
{
	FormItem::text('title', 'Title')->required();
	FormItem::date('date', 'Date')->required();
	FormItem::checkbox('published', 'Published')->required();
	FormItem::image('photo', 'Photo');
	FormItem::ckeditor('text', 'Text')->required();
});