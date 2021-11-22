<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Util ファサード
 * Class UtilFacade
 * @package App\Facades
 */
class UtilFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'util';
    }
}
