<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class UserCertification extends Model
{
    use SoftDeletes;
    use CascadesDeletes;

    protected $guarded = [];

    protected $casts = [
        'commercial_certification_document_id' => 'integer',
        'experience_certification_document_id' => 'integer',
        'training_certification_document_id' => 'integer',
        'influencement_certification_document_id' => 'integer',
        'commercial_certification_verified_at' => 'datetime',
        'experience_certification_verified_at' => 'datetime',
        'training_certification_verified_at' => 'datetime',
        'influencement_certification_verified_at' => 'datetime',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = [];

    // prevent if any exists
    public static $cascade = [];

    // localizables
    public static $localizables = [];

    public function user()
    {
        return $this->morphTo(User::class, 'user');
    }

    public function commercial_certification ()
    {
        return $this->hasOne(Document::class, 'id', 'commercial_certification_document_id');
    }

    public function experience_certification ()
    {
        return $this->hasOne(Document::class, 'id', 'experience_certification_document_id');
    }

    public function training_certification ()
    {
        return $this->hasOne(Document::class, 'id', 'training_certification_document_id');
    }

    public function influencement_certification ()
    {
        return $this->hasOne(Document::class, 'id', 'influencement_certification_document_id');
    }

    public function journalisment_certification ()
    {
        return $this->hasOne(Document::class, 'id', 'journalism_certification_document_id');
    }
}
