<?php
namespace App\Http\Repositories\FederationPresident;
use App\Http\Repositories\Base\BaseInterface;

interface FederationPresidentInterface extends BaseInterface{
    public function models($request);
    public function create ($request, $id);
    public function edit ($request, $id);
}
