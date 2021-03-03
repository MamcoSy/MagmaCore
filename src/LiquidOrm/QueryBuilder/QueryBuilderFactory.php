<?php

declare ( strict_types = 1 );

namespace MagmaCore\LiquidOrm\QueryBuilder;

use MagmaCore\LiquidOrm\QueryBuilder\Exceptions\QueryBuilderException;
use MagmaCore\LiquidOrm\QueryBuilder\Interfaces\QueryBuilderInterface;

class QueryBuilderFactory
{
    public function __construct() {}

    /**
     * @param string $queryBuilderString
     */
    public function create( string $queryBuilderString ): QueryBuilderInterface
    {
        $queryBuilderObject = new $queryBuilderString();

        if ( ! $queryBuilderObject instanceof QueryBuilderInterface ) {
            throw new QueryBuilderException( "{$queryBuilderString} is not a valid query builder." );
        }

        return $queryBuilderObject;
    }

}
