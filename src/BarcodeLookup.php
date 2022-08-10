<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\ProductModel;
use Lsv\EanSearch\Utils\BarcodeUtil;
use Lsv\EanSearch\Utils\LanguageEnum;
use Lsv\EanSearch\Utils\Serializer;

class BarcodeLookup extends Request
{
    private function __construct(
        private readonly string $barcode,
        private readonly LanguageEnum $language
    ) {
    }

    public static function request(string $barcode, LanguageEnum $language = LanguageEnum::DEFAULT): ?ProductModel
    {
        return (new self($barcode, $language))->doRequest();
    }

    protected function getOperation(): string
    {
        return 'barcode-lookup';
    }

    protected function buildUrl(): array
    {
        return [
            BarcodeUtil::getBarcodeTypeCode($this->barcode) => $this->barcode,
            'language' => $this->language->value,
        ];
    }

    protected function parseResponse(string $content): ?ProductModel
    {
        $serialized = Serializer::getSerializer()->deserialize(
            $content,
            ProductModel::class.'[]',
            'json'
        );

        if (!$serialized) {
            return null;
        }

        return $serialized[0];
    }
}
