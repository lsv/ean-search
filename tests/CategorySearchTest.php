<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\CategorySearch;
use Lsv\EanSearch\Request;
use Lsv\EanSearch\Utils\CategoryEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CategorySearchTest extends TestCase
{
    private MockHttpClient $client;

    protected function setUp(): void
    {
        $this->client = new MockHttpClient();
        Request::setApiToken('token');
        Request::setClient($this->client);
    }

    public function testUrl(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/single_product.json')),
        ]);

        CategorySearch::request(CategoryEnum::ART, 'product');
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'category-search',
                'format' => 'json',
                'category' => '90',
                'name' => 'product',
                'language' => '99',
                'page' => 0,
            ],
            CategorySearch::$queries[0]
        );
    }

    public function testCanMultipleProducts(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/multi_products.json')),
        ]);

        $response = CategorySearch::request(CategoryEnum::UNKNOWN);
        self::assertCount(2, $response);
        $product = $response[0];
        self::assertSame('0042286275123', $product->ean);
        self::assertSame('Stephan Remmler, Bananaboat', $product->name);
        self::assertSame('15', $product->categoryId);
        self::assertSame('Music', $product->categoryName);
        self::assertSame('US', $product->issuingCountry);
        $product = $response[1];
        self::assertSame('4011222328366', $product->ean);
        self::assertSame('Harry Belafonte: Bananaboat', $product->name);
        self::assertSame('15', $product->categoryId);
        self::assertSame('Music', $product->categoryName);
        self::assertSame('DE', $product->issuingCountry);
    }

    public function testPaging(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/paging_products.json')),
            new MockResponse(file_get_contents(__DIR__.'/responses/paging1_products.json')),
        ]);

        $response = CategorySearch::request(CategoryEnum::UNKNOWN);
        self::assertCount(2, $response);
        $product = $response[0];
        self::assertSame('0042286275123', $product->ean);
        $product = $response[1];
        self::assertSame('5099750442227', $product->ean);
        self::assertSame('Michael Jackson, Thriller', $product->name);
        self::assertSame('15', $product->categoryId);
        self::assertSame('Music', $product->categoryName);
        self::assertSame('UK', $product->issuingCountry);

        self::assertCount(2, CategorySearch::$queries);
        self::assertSame(0, CategorySearch::$queries[0]['page']);
        self::assertSame(1, CategorySearch::$queries[1]['page']);
    }
}
