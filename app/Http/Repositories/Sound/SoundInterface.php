<?php
namespace App\Http\Repositories\Sound;
use App\Http\Repositories\Base\BaseInterface;

interface SoundInterface extends BaseInterface{

    public function models($request);
    public function create($request);
    public function update($id, $request);
    public function delete($id);
}