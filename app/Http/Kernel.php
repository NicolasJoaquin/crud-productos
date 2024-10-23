<?php

namespace App\Http;

class Kernel
{
    /**
     * Middlewares globales
     */
    protected $middleware = [

    ];

    /**
     * Middlewares específicos para asignar a rutas individuales
     */
    protected $routeMiddleware = [
        'auth.jwt' => \App\Http\Middleware\AuthMiddleware::class,
    ];

    /**
     * Ejecutar los middlewares globales
     */
    public function handle($request, $next)
    {
        foreach($this->middleware as $middleware) {
            $middlewareInstance = new $middleware;
            $request = $middlewareInstance->handle($request, $next);
        }

        return $next($request);
    }
}
