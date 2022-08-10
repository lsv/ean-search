<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\BarcodeImageModel;
use Lsv\EanSearch\Utils\BarcodeUtil;
use Lsv\EanSearch\Utils\Serializer;

class BarcodeImage extends Request
{
    public static function request(string $barcode, ?int $width = null, ?int $height = null): ?BarcodeImageModel
    {
        return (new self($barcode, $width, $height))->doRequest();
    }

    private function __construct(
        private readonly string $barcode,
        private readonly ?int $width,
        private readonly ?int $height
    ) {
    }

    protected function getOperation(): string
    {
        return 'barcode-image';
    }

    protected function buildUrl(): array
    {
        return [
            BarcodeUtil::getBarcodeTypeCode($this->barcode) => $this->barcode,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    protected function parseResponse(string $content): ?BarcodeImageModel
    {
        $serialized = Serializer::getSerializer()->deserialize(
            $content,
            BarcodeImageModel::class.'[]',
            'json'
        );

        if (!$serialized) {
            return null;
        }

        return $serialized[0];
    }
}
