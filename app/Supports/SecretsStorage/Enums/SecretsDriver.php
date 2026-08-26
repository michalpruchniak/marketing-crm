<?php

namespace App\Supports\SecretsStorage\Enums;

enum SecretsDriver: string
{
    case Database = 'db';
    case Hashicorp = 'hashicorp';

}
