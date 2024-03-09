<?php 

namespace App\Http\Repositories\Preference;

use App\Http\Repositories\Base\BaseInterface;

interface PreferenceInterface extends BaseInterface
{
  public function models($request);
}