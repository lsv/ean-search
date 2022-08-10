<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Model;

class VerifyChecksumModel
{
    public string $ean;
    public string $valid;

    public function isVerified(): bool
    {
        return (bool) $this->valid;
    }
}
