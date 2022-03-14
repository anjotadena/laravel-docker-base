<?php
namespace App\Virtual\Request;

/**
 * @OA\Schema(
 *      title="Login request",
 *      description="Login request payload or json data",
 *      type="object",
 *      required={"email", "password"},
 *      @OA\Xml(
 *         name="LoginRequest"
 *     )
 * )
 */
class LoginRequest
{
    /**
     * @OA\Property(
     *      title="email",
     *      description="User email",
     *      example="test@gmail.com"
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *      title="password",
     *      description="User password"
     * )
     *
     * @var string
     */
    public $password;
}
