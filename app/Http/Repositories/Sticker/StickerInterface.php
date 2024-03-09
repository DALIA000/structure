<?php
namespace App\Http\Repositories\Sticker;
use App\Http\Repositories\Base\BaseInterface;

interface StickerInterface extends BaseInterface{
   public function models($request);
   public function create($request);
}
