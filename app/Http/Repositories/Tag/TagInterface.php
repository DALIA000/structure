<?php

namespace App\Http\Repositories\Tag;

interface TagInterface
{
    public function findById($id);

    public function all($request);

    public function create($request);

    public function update($request, $id);

    public function destroy($id);

}
