<?php

declare ( strict_types = 1 );

namespace MagmaCore\Router;

use MagmaCore\Router\Exception\RouterException;
use MagmaCore\Router\Interfaces\RouterInterface;
use MagmaCore\Router\Exception\RouteNotFoundException;

class Router implements RouterInterface
{
    protected array $routeCollection   = [];
    protected array $parameters        = [];
    protected string $controllerSuffix = 'controller';

    /**
     * @inheritDoc
     */
    public function add( string $route, array $parameters ): void
    {
        $this->routeCollection[$route] = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function dispatch( string $url )
    {

        if ( $this->match( $url ) ) {
            $controllerName     = $this->parameters['_controller'];
            $controllerName     = $this->toUpperCamelCase( $controllerName );
            $fullControllerName = $this->getNamespace() . $controllerName;

            if ( class_exists( $fullControllerName ) ) {
                $controllerObject = new $fullControllerName();
                $actionName       = $this->parameters['_action'];
                $actionName       = $this->toCamelCase( $actionName );

                if (
                    method_exists( $controllerObject, $actionName )
                    && is_callable( [$controllerObject, $actionName] )
                ) {
                    call_user_func_array(
                        [$controllerObject, $actionName],
                        []
                    );
                }

                throw new RouterException(
                    "class {$fullControllerName} does not hove method named
                    {$actionName}
                    "
                );

            }

            throw new RouterException(
                "class {$fullControllerName} not exists"
            );
        }

        throw new RouteNotFoundException();
    }

    /**
     * Matche routes with the given url
     * @param  string $url
     * @return bool
     */
    private function match( string $url ): bool
    {

        foreach ( $this->routeCollection as $route => $parameters ) {

            if ( preg_match( "#{$route}#", $url, $matches ) ) {

                foreach ( $matches as $key => $value ) {

                    if ( is_string( $key ) ) {
                        $parameters[$key] = $value;
                    }

                }

                $this->parameters = $parameters;

                return true;
            }

        }

        return false;
    }

    /**
     * Transform the given string to upper camel case
     * @param  string   $string
     * @return string
     */
    private function toUpperCamelCase( string $string ): string
    {
        return str_replace(
            ' ',
            '',
            ucwords( str_replace( '-', '', $string ) )
        );
    }

    /**
     * Transform the given string to camel case
     * @param  string   $string
     * @return string
     */
    private function toCamelCase( string $string ): string
    {
        return lcfirst( $this->toUpperCamelCase( $string ) );
    }

    /**
     * Getting the controller namespace
     * @return string
     */
    private function getNamespace(): string
    {
        $defaultNamespace = "App\\Controllers\\";

        if ( array_key_exists( '_namespace', $this->parameters ) ) {
            $defaultNamespace .= $this->parameters['_namespace'] . '\\';
        }

        return $defaultNamespace;
    }

}
