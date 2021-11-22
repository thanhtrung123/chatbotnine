<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Constant;
use App\Services\Admin\LogService;

/**
 * 操作ログコントローラ
 * Class LogController
 * @package App\Http\Controllers\Admin
 */
class LogController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.log';
    /**
     * @var LogService
     */
    private $service;

    /**
     * LogController constructor.
     * @param LogService $service
     */
    public function __construct(LogService $service)
    {
        $this->service = $service;
    }

    //API

    /**
     * ログ情報一覧(API)
     * @param Request $request
     * @return type
     */
    public function logList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        $datatable->getConverter()
            ->setConvert('processing', function ($val) {
                return Constant::getConstName($val, 'function.admin');
            });
        return response($datatable->toArray());
    }

    //WEB

    /**
     * ログ情報一覧
     * @return type
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index')->with([
            'processing' => Constant::getConstArray('function.admin'),
        ]);
    }
}