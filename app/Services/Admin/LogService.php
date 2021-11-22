<?php

namespace App\Services\Admin;

use App\Repositories\Log\LogRepositoryInterface;
use App\Services\RepositoryServiceInterface;

/**
 * ログサービス
 * Class LogService
 * @package App\Services\Admin
 */
class LogService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var LogRepositoryInterface
     */
    private $repository;

    /**
     * LogService constructor.
     * @param LogRepositoryInterface $repository
     */
    public function __construct(LogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * リポジトリ取得
     * @return LogRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

}