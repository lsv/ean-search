<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\VerifyChecksumModel;
use Lsv\EanSearch\Utils\BarcodeUtil;
use Lsv\EanSearch\Utils\Serializer;

class VerifyChecksum extends Request
{
    public static function request(string $barcode): ?VerifyChecksumModel
    {
        return (new self($barcode))->doRequest();
    }

    private function __construct(private readonly string $barcode)
    {
    }

    protected function getOperation(): string
    {
        return 'verify-checksum';
    }

    protected function buildUrl(): array
    {
        return [
            BarcodeUtil::getBarcodeTypeCode($this->barcode) => $this->barcode,
        ];
    }

    protected function parseResponse(string $content): ?VerifyChecksumModel
    {
        $serialized = Serializer::getSerializer()->deserialize(
            $content,
            VerifyChecksumModel::class.'[]',
            'json'
        );

        if (!$serialized) {
            return null;
        }

        return $serialized[0];
    }
}
