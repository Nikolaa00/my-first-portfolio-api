<?php

namespace App\Enums;

enum AssetType: string
{
    case Stock = 'stock';
    case Crypto = 'crypto';
    case Etf = 'etf';
}
