<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Constant ファサード
 * Class ConstFacade
 * @package App\Facades
 */
class ConstFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'constant';
    }
}