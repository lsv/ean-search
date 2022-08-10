<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\BarcodePrefixSearch;
use Lsv\EanSearch\Exception\EanSearchException;
use Lsv\EanSearch\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class BarcodePrefixSearchTest extends TestCase
{
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

        BarcodePrefixSearch::request('prefix');
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'barcode-prefix-search',
                'format' => 'json',
                'prefix' => 'prefix',
                'language' => '99',
                'page' => 0,
            ],
            BarcodePrefixSearch::$queries[0]
        );
    }

    public function testCanGetProduct(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/single_product.json')),
        ]);

        $response = BarcodePrefixSearch::request('prefix');
        self::assertCount(1, $response);
        $product = $response[0];
        self::assertSame('0042286275123', $product->ean);
        self::assertSame('Stephan Remmler, Bananaboat', $product->name);
        self::assertSame('15', $product->categoryId);
        self::assertSame('Music', $product->categoryName);
        self::assertSame('US', $product->issuingCountry);
    }

    public function testCanMultipleProducts(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/multi_products.json')),
        ]);

        $response = BarcodePrefixSearch::request('prefix');
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

        $response = BarcodePrefixSearch::request('prefix');
        self::assertCount(2, $response);
        $product = $response[0];
        self::assertSame('0042286275123', $product->ean);
        $product = $response[1];
        self::assertSame('5099750442227', $product->ean);
        self::assertSame('Michael Jackson, Thriller', $product->name);
        self::assertSame('15', $product->categoryId);
        self::assertSame('Music', $product->categoryName);
        self::assertSame('UK', $product->issuingCountry);

        self::assertCount(2, BarcodePrefixSearch::$queries);
        self::assertSame(0, BarcodePrefixSearch::$queries[0]['page']);
        self::assertSame(1, BarcodePrefixSearch::$queries[1]['page']);
    }

    public function testHandleNoProducts(): void
    {
        $this->client->setResponseFactory([
            new MockResponse('[]'),
        ]);
        $response = BarcodePrefixSearch::request('prefix');
        self::assertCount(0, $response);
    }

    public function testHandleException(): void
    {
        $this->expectException(EanSearchException::class);

        $this->client->setResponseFactory([
            new MockResponse('', ['http_code' => 404]),
        ]);
        BarcodePrefixSearch::request('prefix');
    }
}
