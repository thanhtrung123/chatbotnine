<?php

namespace App\Services\Admin;

use App\Repositories\LearningRelation\LearningRelationRepositoryInterface;
use App\Services\Bot\BotDbService;
use App\Services\RepositoryServiceInterface;

/**
 * 関連回答サービス
 * Class LearningRelationService
 * @package App\Services\Admin
 */
class LearningRelationService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var LearningRelationRepositoryInterface
     */
    private $repository;
    /**
     * @var BotDbService
     */
    private $bot_db_service;

    /**
     * LearningRelationService constructor.
     * @param BotDbService $bot_db_service
     */
    public function __construct(BotDbService $bot_db_service)
    {
        $this->bot_db_service = $bot_db_service;
        $this->repository = $bot_db_service->getLearningRelationRepository();
    }

    /**
     * リポジトリ取得
     * @return LearningRelationRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return BotDbService
     */
    public function getBotDbService(): BotDbService
    {
        return $this->bot_db_service;
    }

}