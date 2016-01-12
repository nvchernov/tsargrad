<?php

Admin::model(App\Models\Mega::class)->title('Megas')->with()->filters(function ()
{

})->columns(function ()
{
	Column::string('string', 'String');
})->form(function ()
{
	FormItem::text('string', 'String')->required();
	FormItem::text('integer', 'Integer')->required()->validationRule('integer');
	FormItem::image('image', 'Image');
	FormItem::checkbox('boolean', 'Boolean');
	FormItem::date('date', 'Date')->required();;
	FormItem::time('time', 'Time')->required();//->seconds(true);
	FormItem::select('choices', 'Choices')->enum(['foo', 'bar']);
	FormItem::ckeditor('text', 'Text')->required();
});

