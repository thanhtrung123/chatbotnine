<?php

namespace App\Http\Controllers\Admin;

use Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Exports\Admin\EnqueteExport;
use App\Services\Admin\EnqueteService;

/**
 * アンケート回答管理コントローラ
 * Class EnqueteController
 * @package App\Http\Controllers\Admin
 */
class EnqueteController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.enquete';
    /**
     * @var EnqueteService
     */
    private $service;

    /**
     * EnqueteController constructor.
     * @param EnqueteService $service
     */
    public function __construct(EnqueteService $service)
    {
        $this->service = $service;
    }

    //API

    /**
     * アンケート回答一覧(API)
     * @param Request $request
     * @return type
     */
    public function EnqueteList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->setGroup('post_id')->datatable();
        $datatable->getConverter()->setConvert('processing', function ($val) {
            return Constant::getConstName($val, 'function.admin');
        });
        return response($datatable->toArray());
    }

    //WEB

    /**
     * アンケート回答一覧
     * @return type
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index')->with([
            'processing' => Constant::getConstArray('function.admin'),
        ]);
    }

    /**
     * アンケート回答詳細
     * @return type
     */
    public function show(Request $request, $id)
    {
        // アンケートデータ抽出
        $collection = $this->service->getRepository()->setParams(['post_id' => $id])->filterByParams()
            ->filterDecrypt(config('const.enquete.form_id.user_form.id'))->getQuery()->get();
        // IDをラベルに変換し、必要な情報を追加する
        $collection = $this->service->getItemsToLabel($collection, config('const.enquete.form_id.user_form.id'));
        return view(self::ROUTE_NAME . '.show')->with('id', $id)->with('enq_collection', $collection);
    }

    /**
     * エクスポート
     * @param EnqueteExport $export
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(EnqueteExport $export, Request $request)
    {
        // エクスポートログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_export.id'));
        // アンケートデータ抽出
        $form_id = config('const.enquete.form_id.user_form.id');
        $query = $this->service->getRepository()->setParams($request->all())->filterByParams()
            ->filterDecrypt($form_id)->filterExport()->getQuery();
        $export->setQueryAndFormId($query, $form_id);
        $ext = config('excel.exports.extensions');
        return $export->download(str_replace('.', '_', self::ROUTE_NAME) . '_' . date('YmdHis') . $ext)->deleteFileAfterSend(true);
    }
}