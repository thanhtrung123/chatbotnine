<?php

namespace App\Services\Admin;

use App\Repositories\RepositoryInterface;
use App\Repositories\Variant\VariantRepositoryInterface;
use App\Services\RepositoryServiceInterface;

/**
 * 異表記サービス
 * Class VariantService
 * @package App\Services\Admin
 */
class VariantService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    private $repository;

    /**
     * VariantService constructor.
     * @param VariantRepositoryInterface $repository
     */
    public function __construct(VariantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * リポジトリ取得
     * @return VariantRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }
}