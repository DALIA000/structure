<?php

namespace App\Http\Repositories\TrainerExperienceLevel;

interface TrainerExperienceLevelInterface
{
    public function findById($id);

    public function all($request);

    public function create($request);

    public function update($request, $id);

    public function destroy($id);

}
