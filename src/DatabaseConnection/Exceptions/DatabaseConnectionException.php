<?php

declare ( strict_types = 1 );

namespace MagmaCore\DatabaseConnection\Exceptions;

class DatabaseConnectionException
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct( string $message, int $code )
    {
        $this->message = $message;
        $this->code    = $code;
    }
}
