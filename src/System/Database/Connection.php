<?php
declare(strict_types=1);

namespace Api\System\Database;

use PDO;

interface Connection
{
    /**
     * @return PDO
     */
    public function open(): PDO;
}