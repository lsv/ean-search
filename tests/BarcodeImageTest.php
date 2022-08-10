<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\BarcodeImage;
use Lsv\EanSearch\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class BarcodeImageTest extends TestCase
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
            new MockResponse(file_get_contents(__DIR__.'/responses/issuing_country.json')),
        ]);

        BarcodeImage::request('1234567890');
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'barcode-image',
                'format' => 'json',
                'isbn' => '1234567890',
                'width' => null,
                'height' => null,
            ],
            BarcodeImage::$queries[0]
        );
    }

    public function testCanGetProduct(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/barcode_image.json')),
        ]);

        $response = BarcodeImage::request('1234567890');
        self::assertSame('5099750442227', $response->ean);
        self::assertStringStartsWith('iVBORw0KGgoAAAA', $response->barcode);
        self::assertSame('image/png', $response->getContentType());
        self::assertIsString($response->getImage());
        $imageResponse = $response->getResponse();
        self::assertArrayHasKey('content-type', $imageResponse->headers->all());
        self::assertSame($response->getImage(), $imageResponse->getContent());
    }

    public function testHandleNoProducts(): void
    {
        $this->client->setResponseFactory([
            new MockResponse('[]'),
        ]);
        $response = BarcodeImage::request('1234567890');
        self::assertNull($response);
    }
}
