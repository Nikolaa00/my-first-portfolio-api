<?php

namespace App\Enums;

enum Currency: string
{
    case Eur = 'EUR';
    case Usd = 'USD';
    case Gbp = 'GBP';
    case Chf = 'CHF';
    case Jpy = 'JPY';
    case Cad = 'CAD';
    case Aud = 'AUD';
    case Cny = 'CNY';
    case Mkd = 'MKD';
}
