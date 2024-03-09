<?php

namespace App\Http\Repositories\Media;

use App\Http\Repositories\Base\BaseInterface;

interface MediaInterface extends BaseInterface
{
    public function models($request);
    public function upload($request);
}
