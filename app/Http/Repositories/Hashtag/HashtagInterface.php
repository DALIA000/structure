<?php

namespace App\Http\Repositories\Hashtag;

use App\Http\Repositories\Base\BaseInterface;

interface HashtagInterface extends BaseInterface
{
    public function models($request);
}
