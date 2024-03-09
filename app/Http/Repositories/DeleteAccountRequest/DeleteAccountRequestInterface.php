<?php
namespace App\Http\Repositories\DeleteAccountRequest;
use App\Http\Repositories\Base\BaseInterface;

interface DeleteAccountRequestInterface extends BaseInterface{
   public function models($request);
   public function create($request);
}
