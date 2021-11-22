<?php

namespace App\Services\Admin;

use App\Repositories\RepositoryInterface;
use App\Repositories\Synonym\SynonymRepositoryInterface;
use App\Services\RepositoryServiceInterface;

/**
 * 類義語サービス
 * Class SynonymService
 * @package App\Services\Admin
 */
class SynonymService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var SynonymRepositoryInterface
     */
    private $repository;
    /**
     * @var LearningService
     */
    private $learning_service;

    /**
     * SynonymService constructor.
     * @param SynonymRepositoryInterface $repository
     * @param LearningService $learning_service
     */
    public function __construct(SynonymRepositoryInterface $repository, LearningService $learning_service)
    {
        $this->repository = $repository;
        $this->learning_service = $learning_service;
    }

    /**
     * リポジトリ取得
     * @return SynonymRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

}