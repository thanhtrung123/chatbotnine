<?php

namespace App\Services\Admin;

use App\Repositories\ProperNoun\ProperNounRepositoryInterface;
use App\Services\RepositoryServiceInterface;

/**
 * 固有名詞サービス
 * Class ProperNounService
 * @package App\Services\Admin
 */
class ProperNounService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var ProperNounRepositoryInterface
     */
    private $repository;


    /**
     * ProperNounService constructor.
     * @param ProperNounRepositoryInterface $repository
     */
    public function __construct(ProperNounRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * リポジトリ取得
     * @return ProperNounRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function createProperNoun($params)
    {
        $params['proper_noun_id'] = $this->getRepository()->getNextProperNounId();
        return $this->repository->create($params);
    }

}