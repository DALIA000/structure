<?php

namespace App\Services;

use App\Models\CourseSession;
use App\Models\User;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Str;
use App\Http\Resources\FileResource;
use App\Http\Resources\UserFileResource;
use App\Models\File;

class JWTGenerator
{

    public static function generateJWT($model, $user, $is_moderator=false)
    {
        $session = $model->session;
        $roomId = Str::studly($session->id . $session->title);
        $file = $user->files && $user->files[0] ? $user->files[0] : null;

        // The algorithm manager with the HS256 algorithm.
        $algorithmManager = new AlgorithmManager([
            new RS256(),
        ]);

        // Generate a JSON Web Key (JWK).
        $jwk = JWKFactory::createFromKey(env('JAAS_APP_PRIVATE_KEY'));

        // We instantiate our JWS Builder.
        $jwsBuilder = new JWSBuilder($algorithmManager);

        // The payload we want to sign. The payload MUST be a string hence we use our JSON Converter.
        $payload = json_encode([
            'aud' => 'jitsi',
            'context' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->username,
                    'avatar' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
                    'email' => $user->email,
                    'moderator' => $is_moderator
                ],
                'features' => [
                    'livestreaming' => $is_moderator,
                    'recording' => $is_moderator
                ]
            ],
            'nbf' => time(),
            'exp' => time() + 3600,
            'iss' => 'chat',
            'room' => $roomId,
            'sub' => env('JAAS_APP_ID'),
        ]);

        $header = [
            'alg' => 'RS256',
            'kid' => env('JAAS_API_KEY'),
            'typ' => 'JWT'
        ];

        $jws = $jwsBuilder
            ->create()                    // We want to create a new JWS
            ->withPayload($payload)       // We set the payload
            ->addSignature($jwk, $header) // We add a signature with a simple protected header
            ->build();

        $serializer = new CompactSerializer(); // The serializer
        $token = $serializer->serialize($jws, 0); // We serialize the signature at index 0 (we only have one signature).

        return ['token' => $token, 'roomId' => $roomId];
    }

    public static function generate_jwt(User $user, CourseSession $session): String
    {
        $signing_key = "changeme";
        $header = [
            "alg" => "HS512",
            "typ" => "JWT"
        ];
        $header = base64_encode(json_encode($header));
        $payload =  [
            "app_key" => "KoM8k1ZbQNK64ly019exSQ",
            "iat" => time(),
            "exp" => time() + 1800,
            "tokenExp" => time() + 1800,
            'user_identity' => $user?->id,
            'tpc' => $session?->id,
            'role_type' => $session->course?->video?->user_id === $user->id ? 0 : 1,
            'cloud_recording_option' => 1,
            'version' => 1,
        ];

        $payload = base64_encode(json_encode($payload));
        $signature = base64_encode(hash_hmac('sha512', "$header.$payload", $signing_key, true));
        $jwt = "$header.$payload.$signature";

        return $jwt;
    }

    /**
     * per https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555
     */
    function base64_url_encode($text): String
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

}
