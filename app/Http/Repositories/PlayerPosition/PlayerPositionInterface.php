<?php

namespace App\Http\Repositories\PlayerPosition;

interface PlayerPositionInterface
{
    public function findById($id);

    public function models($request);
    
    public function all($request);

    public function create($request);

    public function update($request, $id);

    public function destroy($id);

}
