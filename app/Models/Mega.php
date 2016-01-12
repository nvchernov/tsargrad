<?php

namespace App\Models;

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;

class Mega extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
    use ModelWithImageOrFileFieldsTrait;

    protected $guarded = array('id');

    public function getImageFields()
    {
        return [
            'image' => 'imgs/'
        ];
    }

    public function getDates()
    {
        return array_merge(parent::getDates(), ['date']);
    }
}
