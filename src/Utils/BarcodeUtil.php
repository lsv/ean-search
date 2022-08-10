<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Utils;

use Lsv\EanSearch\Exception\InvalidBarcodeException;

class BarcodeUtil
{
    public static function getBarcodeTypeCode(string $barcode): string
    {
        // @infection-ignore-all
        return match (mb_strlen($barcode)) {
            13 => 'ean',
            12 => 'upc',
            10 => 'isbn',
            default => throw new InvalidBarcodeException($barcode)
        };
    }
}
