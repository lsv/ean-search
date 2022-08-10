<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\PagingModel;
use Lsv\EanSearch\Model\ProductModel;
use Lsv\EanSearch\Utils\LanguageEnum;
use Lsv\EanSearch\Utils\Serializer;

class ProductSearch extends Request
{
    /**
     * @return ProductModel[]
     */
    public static function request(string $productName, LanguageEnum $language = LanguageEnum::DEFAULT, int $page = 0): array
    {
        $request = new self($productName, $language, $page);

        return $request->doRequest();
    }

    private function __construct(
        private readonly string $productName,
        private readonly LanguageEnum $language,
        private readonly int $page
    ) {
    }

    protected function getOperation(): string
    {
        return 'product-search';
    }

    protected function buildUrl(): array
    {
        return [
            'name' => $this->productName,
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
                $this->productName,
                $this->language,
                $data->page + 1
            )
        );
    }
}
