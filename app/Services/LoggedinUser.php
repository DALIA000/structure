<?php

namespace App\Services;

use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;
use App\Events\ForgetPasswordCodeGenerated;
use Str;

class LoggedinUser
{
  public const ADMIN = 'admin';
  public const USER = 'user';

  public static function user()
  {
    $tokenId = SELF::tokenId();
    $tokenRow = Token::where(['id' => $tokenId, 'revoked' => 0])->first();

    if ($tokenRow && (
      $tokenRow->name === SELF::ADMIN ||
      $tokenRow->name === SELF::USER
    )) {
      $provider = config('auth.guards.' . $tokenRow->name . '.provider');
      $model = 'App\\Models\\' . Str::studly(Str::singular($provider));
      $user = $model::find($tokenRow->user_id);
      return $user;
    }

    return null;
  }

  public static function revokeUserRecords($user)
  {
    $tokenRows = Token::where(['user_id' => $user->id, 'name' => strtolower(class_basename($user)), 'revoked' => 0])->get();
    foreach ($tokenRows as $tokenRow) {
      $tokenRow->revoke();
    }

    return true;
  }

  public static function tokenId()
  {
    $bearerToken = request()->bearerToken();
    if (!$bearerToken || $bearerToken == 'null') {
      return null;
    };

    return Configuration::forUnsecuredSigner()->parser()->parse($bearerToken)->claims()->get('jti');
  }

  public static function revoke()
  {
    $tokenId = SELF::tokenId();
    return Token::find($tokenId)->revoke();
  }
}
