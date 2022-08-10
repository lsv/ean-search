<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Model;

use Symfony\Component\HttpFoundation\Response;

class BarcodeImageModel
{
    public string $ean;
    public string $barcode;

    public function getContentType(): string
    {
        return 'image/png';
    }

    public function getImage(): string
    {
        // @infection-ignore-all
        return (string) base64_decode($this->barcode, true);
    }

    public function getResponse(?Response $response = null): Response
    {
        if (!$response) {
            $response = new Response();
        }

        $response->headers->set('content-type', $this->getContentType());
        $response->setContent($this->getImage());

        return $response;
    }
}
