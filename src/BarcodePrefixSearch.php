<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\PagingModel;
use Lsv\EanSearch\Model\ProductModel;
use Lsv\EanSearch\Utils\LanguageEnum;
use Lsv\EanSearch\Utils\Serializer;

class BarcodePrefixSearch extends Request
{
    /**
     * @return ProductModel[]
     */
    public static function request(string $prefix, LanguageEnum $languge = LanguageEnum::DEFAULT, int $page = 0): array
    {
        return (new self($prefix, $languge, $page))->doRequest();
    }

    private function __construct(
        private readonly string $prefix,
        private readonly LanguageEnum $language,
        private readonly int $page
    ) {
    }

    protected function getOperation(): string
    {
        return 'barcode-prefix-search';
    }

    protected function buildUrl(): array
    {
        return [
            'prefix' => $this->prefix,
            'language' => $this->language->value,
            'page' => $this->page,
        ];
    }

    /**
     * @return ProductModel[]
     */
    protected function parseResponse(string $content): array
    {
        /** @var PagingModel $data */
        $data = Serializer::getSerializer()->deserialize($content, PagingModel::class, 'json');
        if (!$data->moreproducts) {
            return $data->productlist;
        }

        return array_merge(
            $data->productlist,
            self::request(
                $this->prefix,
                $this->language,
                $data->page + 1
            )
        );
    }
}
