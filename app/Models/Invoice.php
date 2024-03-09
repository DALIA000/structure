<?php

namespace App\Models;

use App\Traits\Statusable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends EloquentModel
{
    use HasFactory;
    use SoftDeletes;
    use Statusable;

    protected $guarded = [];

    protected $casts = [
        'invocable_id' => 'integer',
        'model_id' => 'integer',
        'status_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model() 
    {
        return $this->hasOne(Model::class);
    }

    public function invoicable()
    {
        return $this->morphTo();
    }
}
