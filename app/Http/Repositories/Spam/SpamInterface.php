<?php

namespace App\Http\Repositories\Spam;

use App\Http\Repositories\Base\BaseInterface;

interface SpamInterface extends BaseInterface
{
    public function models($request);
}
