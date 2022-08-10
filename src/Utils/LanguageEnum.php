<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Utils;

enum LanguageEnum: string
{
    case ENGLISH = '1';
    case DANISH = '2';
    case GERMAN = '3';
    case SPANISH = '4';
    case FINNISH = '5';
    case FRENCH = '6';
    case ITALIAN = '8';
    case DUTCH = '10';
    case NORWEGIAN = '11';
    case POLISH = '12';
    case PORTUGUESE = '13';
    case SWEDISH = '15';
    case DEFAULT = '99';
}
