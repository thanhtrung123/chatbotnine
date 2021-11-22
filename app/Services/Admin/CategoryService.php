<?php

namespace App\Services\Admin;

use App\Repositories\Category\CategoryRepositoryInterface;
use App\Services\RepositoryServiceInterface;

/**
 * カテゴリサービス
 * Class CategoryService
 * @package App\Services\Admin
 */
class CategoryService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * CategoryService constructor.
     * @param CategoryRepositoryInterface $repository
     */
    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * リポジトリ取得
     * @return CategoryRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

}