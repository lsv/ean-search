<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Generator;
use Lsv\EanSearch\BarcodeLookup;
use Lsv\EanSearch\Exception\EanSearchException;
use Lsv\EanSearch\Exception\InvalidBarcodeException;
use Lsv\EanSearch\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class BarcodeLookupTest extends TestCase
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
            new MockResponse(file_get_contents(__DIR__.'/responses/single_ean.json')),
        ]);

        BarcodeLookup::request('1234567890');
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'barcode-lookup',
                'format' => 'json',
                'isbn' => '1234567890',
                'language' => '99',
            ],
            BarcodeLookup::$queries[0]
        );

        parse_str(parse_url(BarcodeLookup::$url, PHP_URL_QUERY), $url);
        self::assertSame(
            BarcodeLookup::$queries[0],
            $url
        );
    }

    public function testCanGetProduct(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/single_ean.json')),
        ]);

        $response = BarcodeLookup::request('1234567890');
        self::assertSame('5099750442227', $response->ean);
        self::assertSame('Michael Jackson, Thriller', $response->name);
        self::assertSame('15', $response->categoryId);
        self::assertSame('Music', $response->categoryName);
        self::assertSame('UK', $response->issuingCountry);
    }

    public function testHandleNoProducts(): void
    {
        $this->client->setResponseFactory([
            new MockResponse('[]'),
        ]);
        $response = BarcodeLookup::request('1234567890');
        self::assertNull($response);
    }

    public function testHandleException(): void
    {
        $this->expectException(EanSearchException::class);

        $this->client->setResponseFactory([
            new MockResponse('', ['http_code' => 404]),
        ]);
        BarcodeLookup::request('1234567890');
    }

    public function dataProviderTypes(): Generator
    {
        yield 'ean' => ['1234567890123', 'ean'];
        yield 'upc' => ['123456789012', 'upc'];
        yield 'isbn' => ['1234567890', 'isbn'];
    }

    /**
     * @dataProvider dataProviderTypes
     */
    public function testTypes(string $barcode, string $type): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/single_ean.json')),
        ]);

        BarcodeLookup::request($barcode);
        self::assertArrayHasKey($type, BarcodeLookup::$queries[0]);
    }

    public function testInvalidType(): void
    {
        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('Barcode "123" is not supported, only EAN, UPC or ISBN is supported');
        BarcodeLookup::request('123');
    }
}
