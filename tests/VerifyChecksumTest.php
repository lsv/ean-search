<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\Request;
use Lsv\EanSearch\VerifyChecksum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class VerifyChecksumTest extends TestCase
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
            new MockResponse(file_get_contents(__DIR__.'/responses/verify_checksum.json')),
        ]);

        VerifyChecksum::request('1234567890123');
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'verify-checksum',
                'format' => 'json',
                'ean' => '1234567890123',
            ],
            VerifyChecksum::$queries[0]
        );
    }

    public function testValidEan(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/verify_checksum.json')),
        ]);

        $response = VerifyChecksum::request('5099750442227');
        self::assertSame('5099750442227', $response->ean);
        self::assertSame('1', $response->valid);
        self::assertTrue($response->isVerified());
    }

    public function testInvalidEan(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/verify_checksum_false.json')),
        ]);

        $response = VerifyChecksum::request('5099750442227');
        self::assertSame('5099750442227', $response->ean);
        self::assertSame('0', $response->valid);
        self::assertFalse($response->isVerified());
    }

    public function testHandleNoResults(): void
    {
        $this->client->setResponseFactory([
            new MockResponse('[]'),
        ]);
        $response = VerifyChecksum::request('1234567890');
        self::assertNull($response);
    }
}
