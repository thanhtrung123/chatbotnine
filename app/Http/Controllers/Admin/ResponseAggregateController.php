<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Constant;
use App\Services\Admin\ResponseAggregateService;
use Util;

/**
 * 応答情報集計コントローラ
 * Class ResponseAggregateController
 * @package App\Http\Controllers\Admin
 */
class ResponseAggregateController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.response_aggregate';
    /**
     * @var ResponseAggregateService
     */
    private $service;

    /**
     * ResponseAggregateController constructor.
     * @param ResponseAggregateService $service
     */
    public function __construct(ResponseAggregateService $service)
    {
        $this->service = $service;
    }

    //API

    /**
     * 概要表示用(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function overviewList(Request $request)
    {
        $type = $request->get('extend')['type'];
        $datatable = $this->service->getOverviewData($type, $request->all());
        return response($datatable->toArray());
    }

    /**
     * ランキング表示用(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function rankingList(Request $request)
    {
        $tab = $request->get('extend')['tab'];
        $datatable = $this->service->getRankingData($tab, $request->all());
        return response($datatable->toArray());
    }
}