<?php

namespace App\Virtual\Resource;
/**
 * @OA\Schema(
 *     title="LoginResource",
 *     description="Login resource",
 *     @OA\Xml(
 *         name="LoginResource"
 *     )
 * )
 */
class LoginResource
{
    /**
     * @OA\Property(
     *     title="token",
     *     description="User auth token"
     * )
     */
    private $token;

    /**
     * @OA\Property(
     *     title="name",
     *     description="User name"
     * )
     */
    private $name;
}
