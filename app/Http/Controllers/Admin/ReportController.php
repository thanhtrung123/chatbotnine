<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use App\Exports\Admin\DashboardExport;
use Carbon\Carbon;
use App\Models\ResponseAggregate;
use Storage;
use File;

/**
 * Report
 * Class ReportController
 * @package App\Http\Controllers\Admin
 * 
 */
class ReportController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.report';
    /**
     * @var ReportService
     */
    private $dashboard_service;

    /**
     * DashboardService constructor.
     * @param DashboardService $service
     */
    public function __construct(DashboardService $dashboard_service) {
        $this->dashboard_service = $dashboard_service;
    }

    /**
     * Display chart dashboard
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request) {
        // Get the start date parameter
        $start_date  = ($request->has('date_s') && $request->get('date_s') != '') ? $request->get('date_s') : Carbon::now()->startOfMonth()->format('Y/m/d');
        // Get the end date parameter
        $end_date  = ($request->has('date_e') && $request->get('date_e') != '') ? $request->get('date_e') : Carbon::now()->endOfMonth()->format('Y/m/d');
        // Check start time ends
        if ($start_date > $end_date) {
            $start_date = $end_date;
            $end_date = ($request->has('date_s') && $request->get('date_s') != '') ? $request->get('date_s') : Carbon::now()->startOfMonth()->format('Y/m/d');
        }
        // Get filter
        $filters = $request->all();
        // State user uses chatbot
        $state_uses_data = $this->dashboard_service->getDataStatistical($filters);
        // Answer State
        $answer_state_data = $this->dashboard_service->getDataAnswerStatistical($filters);
        // Enquete answer
        $enquete_answer_data = $this->dashboard_service->getDataEnqueteStatistical($filters);
        if ($filters['ip'] ?? NULL) {
            $ip = explode(',', $filters['ip']);
        } else {
            $ip = array();
        }
        return view(self::ROUTE_NAME . '.dashboard', compact('filters', 'start_date', 'end_date', 'state_uses_data', 'answer_state_data', 'enquete_answer_data', 'ip'));
    }
    
    /**
     * エクスポート
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request) {
        $filters = $request->all();
        $state_uses_data = $this->dashboard_service->getDataStatistical($filters);
        $answer_state_data = $this->dashboard_service->getDataAnswerStatistical($filters);
        $enquete_answer_data = $this->dashboard_service->getDataEnqueteStatistical($filters);
        $export = new DashboardExport($filters, $state_uses_data, $answer_state_data, $enquete_answer_data);
        //エクスポートログ
        return $export->download(str_replace('.', '_', self::ROUTE_NAME) . '_' . date('YmdHis') . '.xlsx');
    }
    
    /**
     * Upload image into storage
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request) {
        // Status upload success or fail
        $status_upload = $this->dashboard_service->uploadImageStatistics($request->all());
        if (!$status_upload) {
            return response()->json(['error' => 'error'], 400);
        }
        return response()->json(['succsess' => 'succsess', 'data' => $status_upload], 200);
    }
}