<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use App\Services\Admin\ResponseAggregateService;
use App\Services\Admin\EnqueteService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Bot\ResponseInfoService;

/**
 * 管理画面インデックスコントローラ
 * Class IndexController
 * @package App\Http\Controllers\Admin
 */
class IndexController extends Controller
{
    use FormTrait;

    /**
     * @var ResponseInfoService
     */
    private $info_service;
    /**
     * @var ResponseAggregateService
     */
    private $aggregate_service;
    /**
     * @var EnqueteService
     */
    private $enquete_service;

    /**
     * IndexController constructor.
     * @param ResponseInfoService $info_service
     * @param ResponseAggregateService $aggregate_service
     */
    public function __construct(ResponseInfoService $info_service, ResponseAggregateService $aggregate_service, EnqueteService $enquete_service)
    {
        $this->info_service = $info_service;
        $this->aggregate_service = $aggregate_service;
        $this->enquete_service = $enquete_service;
    }

    /**
     * 管理画面インデックス
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $info_repository = $this->info_service->getRepository();
        $aggregate_errors = $this->aggregate_service->getAggregateErrors();
        $enquete_repository = $this->enquete_service->getRepository();

        return view('admin/index')
            ->with([
                'user_count' => $info_repository->getUserCount(),
                'question_count' => $info_repository->getQuestionCount(),
                'enquete_count' => $enquete_repository->getEnqueteAnswerCount(),
            ])
            ->with($this->createDirectError($aggregate_errors));
    }
}