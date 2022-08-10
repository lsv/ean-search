<?php

declare(strict_types=1);

namespace Lsv\EanSearchTest;

use Lsv\EanSearch\BarcodeLookup;
use Lsv\EanSearch\Exception\NoApiTokenException;
use Lsv\EanSearch\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        Request::setClient(HttpClient::create());
        Request::setApiToken('');
    }

    public function testNoApiTokenSet(): void
    {
        $this->expectException(NoApiTokenException::class);
        $this->expectExceptionMessage('No API token is set');

        BarcodeLookup::request('barcode');
    }
}
