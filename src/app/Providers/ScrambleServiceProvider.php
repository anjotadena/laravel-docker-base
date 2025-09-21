<?php

namespace App\Providers;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Server;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class ScrambleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'sanctum')
            );
        });        Scramble::routes(function (Route $route) {
            return str_starts_with($route->uri, 'api/v1/');
        });

        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->info->title = 'Laravel DDD API';
            $openApi->info->description = 'A comprehensive API built with Domain-Driven Design architecture, featuring user authentication and management powered by Laravel Sanctum.';
            $openApi->info->version = '1.0.0';
        });
    }
}
