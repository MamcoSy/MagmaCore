<?php

declare ( strict_types = 1 );

namespace MagmaCore\LiquidOrm\DataMapper;

use MagmaCore\LiquidOrm\DataMapper\Exceptions\DataMapperInvalidArgumentException;

class DataMapperEnvConfiguration
{
    private array $credentials = [];

    /**
     * @param array $credentials
     */
    public function __construct( array $credentials )
    {
        $this->credentials = $credentials;
    }

    /**
     * @param string $driver
     */
    private function isCredentialValid( string $driver )
    {

        if ( empty( $driver ) && ! is_string( $driver ) ) {
            throw new DataMapperInvalidArgumentException(
                "Invalid argument. This either missing or off"
            );
        }

        if ( ! is_array( $this->credentials ) ) {
            throw new DataMapperInvalidArgumentException(
                "Invalid Credentials."
            );
        }

        if ( ! in_array( $driver, array_keys( $this->credentials ) ) ) {

            throw new DataMapperInvalidArgumentException(
                "Invalid or unsupported database driver."
            );
        }

    }

    /**
     * Getting the database credentials for the given driver
     * @param string $driver
     */
    public function getDatabaseCredentials( string $driver ): array
    {
        $credentialArray = [];

        if ( in_array( $driver, $this->credentials ) ) {
            return $this->credentials[$driver];
        }

        return $credentialArray;
    }

}
