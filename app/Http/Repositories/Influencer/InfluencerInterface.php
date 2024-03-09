<?php

namespace App\Http\Repositories\Influencer;

use App\Http\Repositories\Base\BaseInterface;

interface InfluencerInterface extends BaseInterface
{
    public function models($request);
}
