<?php

declare ( strict_types = 1 );

namespace MagmaCore\Router\Interfaces;

interface RouterInterface
{
    /**
     * Adding new route to the routing table
     * @param  string $route
     * @param  array  $parameters
     * @return void
     */
    public function add( string $route, array $parameters ): void;

    /**
     * Dispacth route and create the coresponding object and execute it
     * @param  string  $url
     * @return mixed
     */
    public function dispatch( string $url );
}
