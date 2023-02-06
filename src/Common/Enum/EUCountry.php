<?php

declare(strict_types=1);

namespace App\Common\Enum;

enum EUCountry: string
{
    case AUSTRIA        = 'AT';
    case BELGIUM        = 'BE';
    case BULGARIA       = 'BG';
    case CYPRUS         = 'CY';
    case CZECH_REPUBLIC = 'CZ';
    case GERMANY        = 'DE';
    case DENMARK        = 'DK';
    case ESTONIA        = 'EE';
    case SPAIN          = 'ES';
    case FINLAND        = 'FI';
    case FRANCE         = 'FR';
    case GREECE         = 'GR';
    case CROATIA        = 'HR';
    case HUNGARY        = 'HU';
    case IRELAND        = 'IE';
    case ITALY          = 'IT';
    case LITHUANIA      = 'LT';
    case LUXEMBOURG     = 'LU';
    case LATVIA         = 'LV';
    case MALTA          = 'MT';
    case NETHERLANDS    = 'NL';
    case POLAND         = 'PO';
    case PORTUGAL       = 'PT';
    case ROMANIA        = 'RO';
    case SWEDEN         = 'SE';
    case SLOVENIA       = 'SI';
    case SLOVAKIA       = 'SK';

    public static function isEU(string $countryCode): bool
    {
        return \in_array(EUCountry::tryFrom($countryCode), EUCountry::cases());
    }
}
