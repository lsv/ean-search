<?php

declare(strict_types=1);

namespace Lsv\EanSearch;

use Lsv\EanSearch\Model\AccountStatusModel;
use Lsv\EanSearch\Utils\Serializer;

class AccountStatus extends Request
{
    public static function request(): AccountStatusModel
    {
        return (new self())->doRequest();
    }

    protected function getOperation(): string
    {
        return 'account-status';
    }

    protected function buildUrl(): array
    {
        return [];
    }

    protected function parseResponse(string $content): AccountStatusModel
    {
        return Serializer::getSerializer()->deserialize(
            $content,
            AccountStatusModel::class,
            'json'
        );
    }
}
