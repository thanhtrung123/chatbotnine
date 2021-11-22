<?php
namespace App\Http\Controllers\Admin;

use App\Exports\Admin\ResponseInfoExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Constant;
use App\Services\Bot\ResponseInfoService;

/**
 * 応答情報コントローラ
 * Class ResponseInfoController
 * @package App\Http\Controllers\Admin
 */
class ResponseInfoController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.response_info';
    /**
     * @var ResponseInfoService
     */
    private $service;

    /**
     * ResponseInfoController constructor.
     * @param ResponseInfoService $service
     */
    public function __construct(ResponseInfoService $service)
    {
        $this->service = $service;
    }
    //API

    /**
     * 応答情報一覧(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function responseInfoList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())
                ->filterByParamsForInfoList()->filterInfoList()->datatable();
        $datatable->getConverter()
            ->setConvert(['user_input', 'api_answer', 'api_question'], function ($val) {
                return str_omit_tooltip($val, config('bot.common.str_omit_length'));
            })->setConvert(['status'], function ($val) {
            return Constant::getConstName($val, 'bot.status');
        });
        return response($datatable->toArray());
    }

    /**
     * 応答情報詳細(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function responseInfoDetail(Request $request)
    {
        $db_result = $this->service->getDetail($request->get('id'));
        $db_result->getConverter()
            ->setConvert(['user_input', 'api_answer', 'api_question'], function ($val) {
                return str_omit_tooltip($val, config('bot.common.str_omit_length'));
            })->setConvert(['status'], function ($val) {
            return Constant::getConstName($val, 'bot.status');
        });
        return response($db_result->getArray());
    }

    /**
     * 応答情報(真理表)詳細(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function responseInfoTruthDetail(Request $request)
    {
        $db_result = $this->service->getTruthDetail($request->get('id'));
        $db_result->getConverter()
            ->setConvert(['yes_word', 'no_word'], function ($val) {
                return str_omit_tooltip($val, config('bot.common.str_omit_length'));
            });
        return response($db_result->getArray());
    }

    //WEB

    /**
     * 応答情報一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //チェックボックス
        $chk_feedback = Constant::getConstArray('bot.status', false, ['key' => 'feedback', 'name' => 'name2']); // + ['other' => '無回答'];
        $chk_status = Constant::getConstArray('bot.status', false, ['digit' => config('const.bot.status.question_input.id')]);
        $chk_keyword = [
            'user_input' => '質問文(入力値)',
            'api_question' => '質問文(学習データ)',
            'api_answer' => '回答文',
        ];
        $score_vals = ['' => ''] + range(0, 100, 5);

        return view(self::ROUTE_NAME . '.index')->with([
                'checkbox' => [
                    'feedback' => $chk_feedback,
                    'status' => $chk_status,
                    'keyword' => $chk_keyword,
                ],
                'score' => array_combine($score_vals, $score_vals),
        ]);
    }

    /**
     * エクスポート
     * @param ResponseInfoExport $export
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(ResponseInfoExport $export, Request $request)
    {
        $query = $this->service->getRepository()->setParams($request->all())->filterByParams()->filterExport()->getQuery();
        $export->setQuery($query);
        $ext = config('excel.exports.extensions');
        return $export->download(str_replace('.', '_', self::ROUTE_NAME) . '_' . date('YmdHis') . $ext)->deleteFileAfterSend(true);
    }
}