<?php

use MagmaCore\LiquidOrm\QueryBuilder\QueryBuilder;

require_once './vendor/autoload.php';

$queryBuilder = new QueryBuilder();
$queryBuilder->buildQuery( [
    'conditions'  => ['id' => 2],
    'selectors'   => [],
    'replace'     => false,
    'distinct'    => false,
    'from'        => [],
    'where'       => null,
    'and'         => [],
    'or'          => [],
    'order_by'    => [],
    'fields'      => ['username', 'password', 'role'],
    'primary_key' => 'id',
    'table'       => 'users',
    'type'        => '',
    'raw_query'   => '',
] );
echo $queryBuilder->insertQuery() . '<br>';
echo $queryBuilder->selectQuery() . '<br>';
echo $queryBuilder->updateQuery() . '<br>';
echo $queryBuilder->deleteQuery() . '<br>';
