<?php

namespace App\Virtual\Resource;
/**
 * @OA\Schema(
 *     title="RegisterResource",
 *     description="Register resource",
 *     @OA\Xml(
 *         name="RegisterResource"
 *     )
 * )
 */
class RegisterResource
{
    /**
     * @OA\Property(
     *     title="message",
     *     description="Message"
     * )
     */
    private $message;
}