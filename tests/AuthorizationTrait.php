<?php


namespace Tests;


use App\Models\User;
use Laravel\Passport\Passport;

/**
 * Trait AuthorizationTrait
 * @package Tests
 */
trait AuthorizationTrait
{
    /**
     * @param array $attributes
     * @return string[]
     */
    public function authorizeUser($attributes = []): array
    {
        $user = factory(User::class)->create($attributes);

        $userToken = $user->createToken('Sampler')->accessToken;

        Passport::actingAs($user);

        return ["Authorization" => "Bearer $userToken"];
    }
}