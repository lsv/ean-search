<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\IssuingCountryLookup;
use Lsv\EanSearch\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class IssuingCountryLookupTest extends TestCase
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

        IssuingCountryLookup::request('1234567890');
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'issuing-country',
                'format' => 'json',
                'isbn' => '1234567890',
            ],
            IssuingCountryLookup::$queries[0]
        );

        parse_str(parse_url(IssuingCountryLookup::$url, PHP_URL_QUERY), $url);
        self::assertSame(
            IssuingCountryLookup::$queries[0],
            $url
        );
    }

    public function testCanGetProduct(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/issuing_country.json')),
        ]);

        $response = IssuingCountryLookup::request('1234567890');
        self::assertSame('5099750442227', $response->ean);
        self::assertSame('UK', $response->issuingCountry);
    }

    public function testHandleNoResults(): void
    {
        $this->client->setResponseFactory([
            new MockResponse('[]'),
        ]);
        $response = IssuingCountryLookup::request('1234567890');
        self::assertNull($response);
    }
}
