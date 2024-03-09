<?php

namespace App\Http\Repositories\AcademyLevel;

interface AcademyLevelInterface
{
    public function findById($id);

    public function all($request);

    public function models($request);

    public function create($request);

    public function update($request, $id);

    public function destroy($id);

}
