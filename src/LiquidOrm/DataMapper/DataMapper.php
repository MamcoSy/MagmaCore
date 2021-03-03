<?php

declare ( strict_types = 1 );

namespace MagmaCore\LiquidOrm\DataMapper;

use PDO;
use Throwable;
use PDOStatement;
use MagmaCore\LiquidOrm\DataMapper\Exceptions\DataMapperException;
use MagmaCore\LiquidOrm\DataMapper\Interfaces\DataMapperInterface;
use MagmaCore\DatabaseConnection\Interfaces\DatabaseConnectionInterface;

class DataMapper implements DataMapperInterface
{
    private DatabaseConnectionInterface $databaseConnection;
    private  ? PDOStatement $pdoStatement;

    /**
     * @param DatabaseConnectionInterface $databaseConnection
     */
    public function __construct( DatabaseConnectionInterface $databaseConnection )
    {
        $this->databaseConnection = $databaseConnection;
        $this->pdoStatement       = null;
    }

    /**
     * checking if the given value is empty
     * @param  $value
     * @param  string                $errorMessage
     * @throws DataMapperException
     */
    private function isEmpty( $value, string $errorMessage )
    {

        if ( empty( $value ) ) {
            throw new DataMapperException( $errorMessage );
        }

    }

    /**
     * checking if the given value is array
     * @param  $value
     * @param  string                $errorMessage
     * @throws DataMapperException
     */
    private function isArray( $value )
    {

        if ( ! is_array( $value ) ) {
            throw new DataMapperException( 'values should be an array' );
        }

    }

    /**
     * @inheritDoc
     */
    public function prepare( string $sqlQuery ) : self
    {
        $pdoStatement = $this
            ->databaseConnection
            ->open()
            ->prepare( $sqlQuery )
        ;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBindType( $value )
    {
        try {

            switch ( $value ) {

                case is_bool( $value ):
                case intval( $value ):
                    $type = PDO::PARAM_INT;
                    break;

                case is_null( $value ):
                    $type = PDO::PARAM_NULL;
                    break;

                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            return $type;
        } catch ( Throwable $th ) {
            throw new DataMapperException( $th->getMessage() );
        }

    }

    /**
     * Bind value the the corresponding name or question Mark
     * placeholder in the sql Query
     * @param  array                 $values
     * @throws DataMapperException
     */
    protected function bindValues(
        array $fields,
        bool $isSearch
    ): PDOStatement {
        $this->isArray( $fields );

        foreach ( $fields as $key => $value ) {
            $this
                ->pdoStatement
                ->bindValue(
                    ':' . $key,
                    ( $isSearch === false ) ? $value : '%' . $value . '%',
                    $this->getBindType( $value )
                )
            ;
        }

        return $this->pdoStatement;
    }

    /**
     * @inheritDoc
     */
    public function bindParameters( array $fields, bool $isSearch )
    {
        $this->isArray( $fields );

        if ( $this->bindValues( $fields, $isSearch ) ) {
            return $this;
        }

    }

    /**
     * @inheritDoc
     */
    public function execute()
    {

        if ( $this->pdoStatement ) {
            return $this->pdoStatement->execute();
        }

    }

    /**
     * @inheritDoc
     */
    public function numberOfRows(): int
    {

        if ( $this->pdoStatement ) {
            return (int) $this->pdoStatement->rowCount();
        }

    }

    /**
     * @inheritDoc
     */
    public function fetchOne(): object
    {

        if ( $this->pdoStatement ) {
            return $this->pdoStatement->fetch( PDO::FETCH_OBJ );
        }

    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): array
    {

        if ( $this->pdoStatement ) {
            return $this->pdoStatement->fetchAll( PDO::FETCH_ASSOC );
        }

    }

    /**
     * @inheritDoc
     */

    public function lastInsertedId(): int
    {
        try {
            $connection = $this->databaseConnection->open();

            if ( $connection ) {
                $lastId = $connection->lastInsertId();

                if ( ! empty( $lastId ) ) {
                    return (int) $lastId;
                }

            }

        } catch ( Throwable $th ) {
            throw new DataMapperException( $th->getMessage() );
        }

    }

}
