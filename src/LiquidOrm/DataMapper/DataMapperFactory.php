<?php

declare ( strict_types = 1 );

namespace MagmaCore\LiquidOrm\DataMapper;

use MagmaCore\LiquidOrm\DataMapper\Exceptions\DataMapperException;
use MagmaCore\DatabaseConnection\Interfaces\DatabaseConnectionInterface;

class DataMapperFactory
{
    public function __construct() {}

    /**
     * @param DatabaseConnectionInterface $databaseConnection
     * @param DataMapperEnvConfiguration  $dataMapperEnvConfiguration
     */
    public function create(
        string $databaseConnectionString,
        string $dataMapperEnvConfigurationString
    ) {

        $credentials = ( new $dataMapperEnvConfigurationString( [] ) )
            ->getDatabaseCredentials( 'mysql' )
        ;
        $databaseConnection = new
            $databaseConnectionString( $credentials );

        if ( ! $databaseConnection instanceof DatabaseConnectionInterface ) {
            throw new DataMapperException(
                "
                {$databaseConnectionString} does not implement
                DatabaseConnection
                "
            );
        }

    }

}
