<?php

namespace App\Enums;

use App\Helpers\EnumHelper;

enum LeadLabel: string
{
    use EnumHelper;

    case New = 'new';
    case ToContact = 'to_contact';
    case Interested = 'interested';
    case OfferSent = 'offer_sent';
    case Negotiations = 'negotiations';
    case Rejected = 'rejected';
    case Acquired = 'acquired';
}
