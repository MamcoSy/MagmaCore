<?php

declare ( strict_types = 1 );

namespace MagmaCore\Router;

use MagmaCore\Router\Exceptions\RouterException;
use MagmaCore\Router\Interfaces\RouterInterface;
use MagmaCore\Router\Exceptions\RouteNotFoundException;

class Router implements RouterInterface
{
    protected array $routeCollection   = [];
    protected array $parameters        = [];
    protected array $matches           = [];
    protected string $controllerSuffix = 'controller';

    const MAP_PATTERN = [
        '#{int:([a-zA-Z\-]+)}#'    => '([0-9]+)',
        '#{string:([a-zA-Z\-]+)}#' => '([a-zA-Z]+)',
        '#{\*:([a-zA-Z\-]+)}#'     => '([0-9a-zA-Z\-]+)',
    ];

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
            $controllerName     = $this->parameters['controller'] . ucfirst( $this->controllerSuffix );
            $controllerName     = $this->toUpperCamelCase( $controllerName );
            $fullControllerName = $this->getNamespace() . $controllerName;

            if ( class_exists( $fullControllerName ) ) {
                $controllerObject = new $fullControllerName();
                $actionName       = $this->parameters['action'];
                $actionName       = $this->toCamelCase( $actionName );

                if (
                    method_exists( $controllerObject, $actionName )
                    && is_callable( [$controllerObject, $actionName] )
                ) {
                    $parametersToPass = isset( $this->parameters['attributes'] ) ? array_merge( $this->matches, $this->parameters['attributes'] ) : $this->matches;

                    return call_user_func_array(
                        [$controllerObject, $actionName],
                        $parametersToPass
                    );
                }

                throw new RouterException( "class {$fullControllerName} does not have method named {$actionName}" );

            }

            throw new RouterException( "class {$fullControllerName} not exists" );
        }

        throw new RouteNotFoundException();
    }

    /**
     * Match routes with the given url
     * @param  string $url
     * @return bool
     */
    private function match( string $url ): bool
    {

        foreach ( $this->routeCollection as $route => $parameters ) {
            $routePattern = $this->getPattern( $route );

            if ( preg_match( $routePattern, $url, $matches ) ) {

                foreach ( $matches as $key => $value ) {

                    if ( is_string( $key ) ) {
                        $parameters[$key] = $value;
                    }

                }

                $parameters['_route'] = $matches[0];
                unset( $matches[0] );
                $this->matches    = $matches;
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

        if ( array_key_exists( 'namespace', $this->parameters ) ) {
            $defaultNamespace .= $this->parameters['namespace'] . '\\';
        }

        return $defaultNamespace;
    }

    /**
     * Generating the regex pattern
     * @param  string  $path
     * @return mixed
     */
    private function getPattern( string $path )
    {
        $pattern = '#^' . preg_replace( '#\/#', '\/', $path ) . '$#';

        foreach ( self::MAP_PATTERN as $map => $mapValue ) {
            $pattern = preg_replace( $map, $mapValue, $pattern );
        }

        return $pattern;
    }

}
