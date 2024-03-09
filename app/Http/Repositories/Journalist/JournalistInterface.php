<?php

namespace App\Http\Repositories\Journalist;

use App\Http\Repositories\Base\BaseInterface;

interface JournalistInterface extends BaseInterface
{
    public function models($request);
}
