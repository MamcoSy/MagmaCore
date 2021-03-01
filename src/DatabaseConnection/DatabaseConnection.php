<?php

declare ( strict_types = 1 );

namespace MagmaCore\DatabaseConnection;

use PDO;
use PDOException;
use MagmaCore\DatabaseConnection\Exceptions\DatabaseConnectionException;
use MagmaCore\DatabaseConnection\Interfaces\DatabaseConnectionInterface;

class DatabaseConnection implements DatabaseConnectionInterface
{
    protected  ? PDO $pdoInstance;
    protected array $credentials;
    protected array $pdoParameters = [
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => true,
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    /**
     * @param array $credentials
     */
    public function __construct( array $credentials )
    {
        $this->credentials = $credentials;
        $this->pdoInstance = null;
    }

    /**
     * @inheritDoc
     */
    public function open() : PDO
    {

        if ( $this->pdoInstance ) {

            try {
                $this->pdoInstance = new PDO(
                    $this->credentials['dsn'],
                    $this->credentials['username'],
                    $this->credentials['password'],
                    $this->pdoParameters
                );
            } catch ( PDOException $e ) {
                throw new DatabaseConnectionException(
                    $e->getMessage(),
                    (int) $e->getCode()
                );
            }

        }

        return $this->pdoInstance;
    }

    /**
     * @inheritDoc
     */
    public function close(): void {}

}
