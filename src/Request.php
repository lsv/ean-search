<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Exception\NoApiTokenException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class Request
{
    private const BASEURL = 'https://api.ean-search.org/api';

    private static string $apiToken = '';
    private static ?HttpClientInterface $client = null;

    /**
     * @var array<array<string, int|string|null>>
     */
    public static array $queries = [];
    public static string $url = '';
    public static ?ResponseInterface $response = null;

    public static function setClient(HttpClientInterface $client): void
    {
        self::$client = $client;
        self::$queries = [];
        self::$response = null;
    }

    public static function setApiToken(string $apiToken): void
    {
        self::$apiToken = $apiToken;
    }

    /**
     * @throws Exception\HandleRequestException
     * @throws Exception\TooManyRedirectException
     * @throws Exception\EanSearchException
     */
    protected function doRequest(): mixed
    {
        $query = $this->buildQuery();
        self::$queries[] = $query;
        try {
            self::$response = self::getClient()->request(
                $this->getHttpMethod(),
                self::BASEURL,
                [
                    'query' => $query,
                ]
            );

            self::$url = self::$response->getInfo('url');

            return $this->parseResponse(self::$response->getContent());
        } catch (TransportExceptionInterface $exception) {
            // @codeCoverageIgnoreStart
            // @infection-ignore-all
            throw new Exception\HandleRequestException($exception->getMessage(), $exception->getCode(), $exception);
            // @codeCoverageIgnoreEnd
        } catch (RedirectionExceptionInterface $exception) {
            // @codeCoverageIgnoreStart
            // @infection-ignore-all
            throw new Exception\TooManyRedirectException($exception->getMessage(), $exception->getCode(), $exception);
            // @codeCoverageIgnoreEnd
        } catch (ClientExceptionInterface|ServerExceptionInterface $exception) {
            throw new Exception\EanSearchException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @return array<string, int|string|null>
     */
    private function buildQuery(): array
    {
        return array_merge([
            'token' => $this->getToken(),
            'op' => $this->getOperation(),
            'format' => 'json',
        ], $this->buildUrl());
    }

    private function getHttpMethod(): string
    {
        return 'GET';
    }

    abstract protected function getOperation(): string;

    /**
     * @return array<string, int|string|null>
     */
    abstract protected function buildUrl(): array;

    abstract protected function parseResponse(string $content): mixed;

    private function getToken(): string
    {
        if (!self::$apiToken) {
            throw new NoApiTokenException();
        }

        return self::$apiToken;
    }

    private function getClient(): HttpClientInterface
    {
        return self::$client ?: HttpClient::create();
    }
}
