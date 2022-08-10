<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\AccountStatus;
use Lsv\EanSearch\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class AccountStatusTest extends TestCase
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
            new MockResponse(file_get_contents(__DIR__.'/responses/account_status.json')),
        ]);

        AccountStatus::request();
        self::assertSame(
            [
                'token' => 'token',
                'op' => 'account-status',
                'format' => 'json',
            ],
            AccountStatus::$queries[0]
        );
    }

    public function testCanGetProduct(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/responses/account_status.json')),
        ]);

        $response = AccountStatus::request();
        self::assertSame('3', $response->id);
        self::assertSame(2, $response->requests);
        self::assertSame(100, $response->requestlimit);
    }
}
