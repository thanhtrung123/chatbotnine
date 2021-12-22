<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\WysiwygFileUploadService;

/**
 * Wysiwyg editor
 * Class WysiwygFileUploadController
 * @package App\Http\Controllers\Admin
 * 
 */
class WysiwygFileUploadController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.wysiwyg';
    
    /**
     * @var WysiwygFileUploadService
     */
    private $service;

    /**
     * WysiwygFileUploadController constructor.
     * @param WysiwygFileUploadService $service
     */
    public function __construct(WysiwygFileUploadService $service)
    {
        $this->service = $service;
    }

    /**
     * Display wysiwyg image upload
     * @param Request $request
     * @return @view
     */
    public function wysiwygImage(Request $request)
    {
        return view('admin.wysiwyg.image.image');
    }
    
    /**
     * Display wysiwyg editor image list
     * @param Request $request
     * @return @view
     */
    public function wysiwygImageList(Request $request)
    {
        // Process get data image
        $data = $this->service->getDataImage($request->all());
        return view('admin.wysiwyg.image.image_list', [
            'data_image' => $data['data_image'],
            'search' => $data['search'],
            'maxRow' => (int) $data['maxRow']
        ]);
    }

    /**
     * Process upload image
     * @param Request $request
     * @return @redirect
     */
    public function wysiwygImageUpload(Request $request)
    {
        $data = $request->all();
        $error_message = $this->service->processUploadImage($request->all());
        if ($error_message == '') {
            return redirect()->route(self::ROUTE_NAME . '.list')->with('success_message', config('message.save_upload_sucess'));
        }
        return redirect()->route(self::ROUTE_NAME . '.image')->withErrors(['error_message'  => $error_message])->withInput();
    }
    
    /**
     * Display property image
     * @param Request $request
     * @return @redirect
     */
    public function wysiwygImageProperty(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            // Get data image info
            $property = $this->service->getRepository()->getOneById($id);
            if ($property) {
                return view('admin.wysiwyg.image.image_property', [
                    'property' => $property
                    ]);
            }
        }
        return redirect()->back();
    }

    /**
     * Save property image
     * @param Request $request
     * @return @redirect
     */
    public function wysiwygImagePropertySave(Request $request)
    {
        // POST値がある場合
        if ($request->get('image_id') && $request->get('image_name')) {
            $request->session()->regenerateToken();
            $params = $request->all();
            $id = $request->get('image_id');
            $mess = $this->service->updateImageInfo($id, $params);
            if ($mess == '') {
                return redirect()->route(self::ROUTE_NAME . '.property', ['id' => $id])->with('submit_flg', config('wysiwyg.config.flag_on'));
            } else {
                return redirect()->route(self::ROUTE_NAME . '.property', ['id' => $id])->withErrors(['error_message'  => $mess]);
            }
        }
        return redirect()->back();
    }

    /**
     * Remove image
     * @param Request $request
     * @return @response
     */
    public function wysiwygImageDelete(Request $request)
    {
        $err_msg = "";
        $id = $request->get('image_id');
        if ($id) {
            $del_img = $this->service->wysiwygImageDelete($id);
            if ($del_img == false) {
                $err_msg = config('message.del_image_fail');
            }
        }
        return response($err_msg);
    }
    
    /**
     * Wysiwyg image Controll
     * @param Request $request
     * @return @view
     */
    public function wysiwygImageControll(Request $request)
    {
        // Get data image controll
        $data_result = $this->service->wysiwygImageControll($request->all());
        if ($data_result['status'] == TRUE) {
            return view('admin.wysiwyg.image.image_controll', [
                'image_id' => $data_result['data']['image_id'],
                'image' => $data_result['data']['image'],
                'db_value' => $data_result['data']['db_value'],
                'errMsg' => $data_result['data']['errMsg'],
                'sucess_msg' => $data_result['data']['sucess_msg'],
                'imgUseFlg' => $data_result['data']['imgUseFlg'],
                'edit_id' => $data_result['data']['edit_id'],
                'retWidth' => $data_result['data']['retWidth'],
                'retHeight' => $data_result['data']['retHeight'],
                'endFlg' => $data_result['data']['endFlg'],
                'exitFlg' => $data_result['data']['exitFlg'],
                'rewriteEnable' => $data_result['data']['rewriteEnable'],
                'rewriteFlg' => $data_result['data']['rewriteFlg']
            ]);
        }
        return redirect()->back()->withErrors(['error_message' => $data_result['message']]);
    }

    /**
     * Wysiwyg image Refresh
     * @param Request $request
     * @return @response
     */
    public function wysiwygImageRefresh(Request $request)
    {
        $edit_id = $request->get('edit_id');
        $message = '';
        if ($edit_id != '') {
            $results = $this->service->wysiwygImageRefresh($edit_id);
            if ($results) {
                $message = config('message.refresh_success');
                return response(array('alert' => $message));
            }
        }
        $message = config('message.refresh_errors');
        return response(array('alert' => $message));
    }

    /**
     * Wysiwyg image Rename
     * @param Request $request
     * @return @response
     */
    public function wysiwygImageRename(Request $request)
    {
        // Get data
        $data = $request->all();
        $ret = $this->service->wysiwygImageRename($data['ext'], $data['new_file_name'], $data['new_name']);
        return response($ret);
    }
    
    /**
     * Wysiwyg image check path
     * @param Request $request
     * @return @response
     */
    public function wysiwygImageCheck(Request $request)
    {
        $file_name = $request->has('filename') ? $request->get('filename') : [];
        $duplicates = array_diff_assoc($file_name, array_unique($file_name));
        $file_ary = [];
        if (!empty($file_name)) {
            foreach ($file_name as $key => $value) {
                if (is_file(public_path(env('QA_IMAGES_PATH') . '/' . $value))) {
                    $file_ary[$key+1] = $value;
                }
            }
        }
        if (!empty($duplicates)) {
            foreach ($duplicates as $i => $value) {
                if (!in_array($value, $file_ary)) {
                    $file_ary[$i+1] = $value;
                }
            }
        }
        return response(array('datas' => $file_ary));
    }
}