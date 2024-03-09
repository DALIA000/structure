<?php

namespace App\Http\Repositories\Video;

use App\Http\Repositories\Base\BaseInterface;

interface VideoInterface extends BaseInterface
{
    public function models($request);
    public function deleteVideo($id, $request);
}
