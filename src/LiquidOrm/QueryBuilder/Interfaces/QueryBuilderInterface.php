<?php

declare ( strict_types = 1 );

namespace MagmaCore\LiquidOrm\QueryBuilder\Interfaces;

interface QueryBuilderInterface
{
    public function insertQuery(): string;
    public function selectQuery(): string;
    public function updateQuery(): string;
    public function deleteQuery(): string;
}
