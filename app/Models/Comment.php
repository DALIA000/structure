<?php

namespace App\Models;

use App\Traits\Likable;
use App\Traits\Modelable;
use App\Traits\Commentable;
use App\Traits\Reportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Comment extends EloquentModel
{
    use HasFactory, SoftDeletes, CascadesDeletes;
    use Modelable {
        Modelable::model as selfModel;
    }
    use Likable;
    use Commentable;
    use Reportable;

    protected $table = 'comments';

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'commentable_id' => 'integer',
        'model_id' => 'integer',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = ['likes', 'comments'];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
    ];

    public function commentable() 
    {
        return $this->morphTo();
    } 

    public function model()
    {
        return $this->belongsTo(Model::class, 'commentable_type', 'type');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
