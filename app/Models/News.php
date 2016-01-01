<?php
namespace App\Models;

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;

class News extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
    use ModelWithImageOrFileFieldsTrait;

    protected $table = 'news';
    protected $fillable = [
        'title',
        'date',
        'published',
        'text',
        'photo',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getImageFields()
    {
        return [
            'photo' => 'news/'
        ];
    }

    public function getDates()
    {
        return array_merge(parent::getDates(), ['date']);
    }

    public function scopeLast($query)
    {
        $query->orderBy('date', 'desc')->limit(4);
    }
}