<?php

declare(strict_types=1);

namespace Lsv\EanSearch\Utils;

enum CategoryEnum: string
{
    case UNKNOWN = '0';
    case AUTO_AND_BOAT = '10';
    case BOOKS = '15';
    case CLOTHING_AND_FASHION = '20';
    case ELECTRONICS = '25';
    case COMPUTER_HARD_AND_SOFTWARE = '251';
    case FOOD = '30';
    case HEALTH_AND_BEAUTY = '35';
    case JEWELRY = '351';
    case HOME_AND_GARDEN = '40';
    case MUSIC = '45';
    case SPORTS = '50';
    case TOYS = '55';
    case FILMS_AND_MOVIES = '60';
    case OFFICE = '65';
    case BABY = '70';
    case PETS_AND_ANIMALS = '75';
    case TRAVEL = '80';
    case LUGGAGE = '801';
    case ART = '90';
}
