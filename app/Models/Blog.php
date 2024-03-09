<?php

namespace App\Models;

use App\Traits\Likable;
use App\Traits\Modelable;
use App\Traits\Savable;
use App\Traits\Sharable;
use App\Traits\Statusable;
use App\Traits\Viewable;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model implements \Spatie\MediaLibrary\HasMedia
{
    use HasFactory; 
    use \App\Traits\Localizable; 
    use \App\Traits\MediaTrait; 
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use Likable;
    use Modelable;
    use Viewable;
    use Savable;
    use Commentable;
    use Sharable;
    use Savable;
    use Statusable;

    protected $guarded = ['id'];

    protected $casts = [
        'status_id' => 'integer',
    ];
    // relationships

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
