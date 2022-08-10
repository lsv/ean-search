<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\IssuingCountryModel;
use Lsv\EanSearch\Utils\BarcodeUtil;
use Lsv\EanSearch\Utils\Serializer;

class IssuingCountryLookup extends Request
{
    public static function request(string $barcode): ?IssuingCountryModel
    {
        return (new self($barcode))->doRequest();
    }

    private function __construct(
        private readonly string $barcode
    ) {
    }

    protected function getOperation(): string
    {
        return 'issuing-country';
    }

    protected function buildUrl(): array
    {
        return [
            BarcodeUtil::getBarcodeTypeCode($this->barcode) => $this->barcode,
        ];
    }

    protected function parseResponse(string $content): ?IssuingCountryModel
    {
        $serialized = Serializer::getSerializer()->deserialize(
            $content,
            IssuingCountryModel::class.'[]',
            'json'
        );

        if (!$serialized) {
            return null;
        }

        return $serialized[0];
    }
}
