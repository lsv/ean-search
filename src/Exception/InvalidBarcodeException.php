<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Exception;

class InvalidBarcodeException extends EanSearchException
{
    public function __construct(string $barcode)
    {
        $message = sprintf('Barcode "%s" is not supported, only EAN, UPC or ISBN is supported', $barcode);
        parent::__construct($message);
    }
}
