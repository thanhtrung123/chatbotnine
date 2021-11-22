<?php

namespace App\Repositories\SnsUidMap;

use App\Repositories\RepositoryInterface;

/**
 * Interface SnsUidMapRepositoryInterface
 * @package App\Repositories\SnsUidMap
 */
interface SnsUidMapRepositoryInterface extends RepositoryInterface
{
    /**
     * @return mixed
     */
    public function getUniqueEnqueteKey();
}