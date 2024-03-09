<?php

namespace App\Http\Repositories\Document;

use App\Http\Repositories\Base\BaseInterface;

interface DocumentInterface extends BaseInterface
{
    public function models($request);
    public function upload($request);
}
