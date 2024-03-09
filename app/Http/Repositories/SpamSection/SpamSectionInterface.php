<?php

namespace App\Http\Repositories\SpamSection;

use App\Http\Repositories\Base\BaseInterface;

interface SpamSectionInterface extends BaseInterface
{
    public function models($request);
}
