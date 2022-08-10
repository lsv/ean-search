<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Exception;

class NoApiTokenException extends EanSearchException
{
    public function __construct()
    {
        $message = 'No API token is set';
        parent::__construct($message);
    }
}
