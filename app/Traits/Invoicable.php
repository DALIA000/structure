<?php

namespace App\Traits;

use App\Models\{
    Invoice,
};

trait Invoicable
{
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoicable');
    }
    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'invoicable');
    }
}
