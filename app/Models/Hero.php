<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Hero extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'heroes';

    protected $fillable = ['name',  'order'];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

}
