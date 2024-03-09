<?php

namespace App\Models;

use App\Models\Like;
use App\Traits\Modelable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Hashtag extends EloquentModel
{
    use HasFactory;
    use Modelable;

    protected $guarded = [];

    protected $casts = [
        'hashtagable_id' => 'integer',
        'model_id' => 'integer',
    ];

    public function hashtagable () 
    {
        return $this->belongsTo(Model::find($this->model_id)?->type, 'hashtagable_id');
    }
}
