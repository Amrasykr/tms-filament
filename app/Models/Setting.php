<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table  = 'fblog_settings';

    protected $fillable = ['title', 'description', 'organization_name', 'logo', 'favicon', 'quick_links'];
}
