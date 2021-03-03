<?php

declare ( strict_types = 1 );

namespace MagmaCore\DatabaseConnection\Interfaces;

use PDO;

interface DatabaseConnectionInterface
{
    /**
     * Open a new Database connection
     * @return PDO
     */
    public function open(): PDO;

    /**
     * Close the current Database connection
     * @return void
     */
    public function close(): void;

    /**
     * Check if connection is closed
     * @return bool
     */
    public function is_closed(): bool;
}
