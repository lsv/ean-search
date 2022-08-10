<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Model;

class PagingModel
{
    public int $page = 0;
    public bool $moreproducts = false;
    public int $totalproducts = 0;
    /**
     * @var ProductModel[]
     */
    public array $productlist = [];
}
