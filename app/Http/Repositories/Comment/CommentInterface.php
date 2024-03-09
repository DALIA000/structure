<?php

namespace App\Http\Repositories\Comment;

use App\Http\Repositories\Base\BaseInterface;

interface CommentInterface extends BaseInterface
{
    public function models($request);
}
