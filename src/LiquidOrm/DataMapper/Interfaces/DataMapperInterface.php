<?php

declare ( strict_types = 1 );

namespace MagmaCore\LiquidOrm\DataMapper\Interfaces;

interface DataMapperInterface
{
    /**
     * Prepare the sql query
     * @param  string $sqlQuery
     * @return self
     */
    public function prepare( string $sqlQuery ): self;

    /**
     * Returns the corresponding PDO::PARAM type
     * @param  mixed                 $value
     * @throws DataMapperException
     * @return mixed
     */
    public function getBindType( $value );

    /**
     * Bind parameters to the prepare query
     * @param  array                 $parameters
     * @param  boolean               $is_search
     * @throws DataMapperException
     * @return void
     */
    public function bindParameters( array $parameters, bool $is_search );

    /**
     * Return number of rows affected by (SELECT, UPDATE, DELETE, INSERT) query
     * @return integer
     */
    public function numberOfRows(): int;

    /**
     * Execute the prepared query
     * @return mixed
     */
    public function execute();

    /**
     * Return one row as an object
     * @return object
     */
    public function fetchOne(): object;

    /**
     * Return all rows as an associative array
     * @return array
     */
    public function fetchAll(): array;

    /**
     * Return the last inserted id
     * @throws DataMapperException
     * @return int
     */
    public function lastInsertedId(): int;
}
