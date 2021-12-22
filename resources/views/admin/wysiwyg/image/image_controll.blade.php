<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Content-Style-Type" content="text/css">
        <meta http-equiv="Content-Script-Type" content="text/javascript">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>画像編集</title>
        <base target="_self"/>
        <link rel="stylesheet" href="{{ asset('/ckeditor/gd_files/css/dialog.css') }}" type="text/css">
        <link rel="stylesheet" href="{{ asset('/ckeditor/style/cropper.css') }}" type="text/css">
        <script src="{{ asset(mix('js/wysiwyg.js')) }}" type="text/javascript"></script>
        <script>
            var $ = jQuery.noConflict();
        </script>
        <script type="text/javascript">
        var url_refresh = "{{ route('admin.wysiwyg.refresh') }}";
        var url_rename = "{{ route('admin.wysiwyg.rename') }}";
        // 編集モード
        var RESIZE_MODE = "{{ config('wysiwyg.config.resize_mode') }}";
        var TRIMMING_MODE = "{{ config('wysiwyg.config.trimming_mode') }}";
        var CANCEL_MODE = "{{ config('wysiwyg.config.cancel_mode') }}";
        var DECISION_MODE = "{{ config('wysiwyg.config.decision_mode') }}";
        
        // 編集画像情報
        var IMAGE_MAX_X = {{ $image['width'] }};
        var IMAGE_MAX_Y = {{ $image['height'] }};
        
        // ディスプレイの画面サイズ
        var SCREEN_WIDTH = screen.width;
        var SCREEN_HEIGHT = screen.height;
        
        // 表示するダイアログサイズ
        var WINDOW_X_MIN = 560; // 横幅	最小
        var WINDOW_X_MAX = 980; // 最大
        var WINDOW_Y_MIN = 570; // 高さ	最小
        var WINDOW_Y_MAX = 690; // 最大
        
        var windowSizeX = (IMAGE_MAX_X + 217);
        var windowSizeY = (IMAGE_MAX_Y + 137);
        
        // 横幅の調整
        if (windowSizeX < WINDOW_X_MIN) {
            windowSizeX = WINDOW_X_MIN;
        }
        if (windowSizeX > WINDOW_X_MAX) {
            windowSizeX = WINDOW_X_MAX;
        }
        // 縦幅の調整
        if (windowSizeY < WINDOW_Y_MIN) {
            windowSizeY = WINDOW_Y_MIN;
        }
        if (windowSizeY > WINDOW_Y_MAX) {
            windowSizeY = WINDOW_Y_MAX;
        }
        
        // ウィンドウサイズ変更
        window.dialogWidth = windowSizeX + "px";
        window.dialogHeight = windowSizeY + "px";
        // ウィンドウ位置調整(スクリーンの中心)
        window.dialogLeft = (SCREEN_WIDTH) / 2 - (windowSizeX / 2);
        window.dialogTop = (SCREEN_HEIGHT) / 2 - (windowSizeY / 2);
        
        // 処理終了フラグ
        var endFlg = "{{ $endFlg }}";
        var exitFlg = "{{ $exitFlg }}";
        // エラーメッセージ
        var sucessMsg = "{{ $sucess_msg }}";
        var errMsg = "{{ $errMsg }}";
        // 画像使用フラグ
        var imgUseFlg = "{{ $imgUseFlg }}";
        
        // 戻値をセット
        var retObj = new Object();
        retObj['id'] = "{{ $image_id }}";
        retObj['path'] = "{{ javaStringEscape($image['path']) }}";
        retObj['edit_id'] = "{{ javaStringEscape($edit_id) }}";
        retObj['width'] = "{{ $retWidth }}";
        retObj['height'] = "{{ $retHeight }}";
        
        //リセット用に画像名称とファイル名を保持
        var olg_file_name = "{{ javaStringEscape($db_value['name']) }}";
        var olg_file_path = "{{ htmlspecialchars(substr(basename($db_value['path']), 0, strrpos(basename($db_value['path']), "."))) }}";
        var err_msg = "{{ ($errors->has('error_message')) ? $errors->first('error_message') : '' }}";
        if (err_msg != '') {
            alert(err_msg);
        }
        </script>
   </head>
   <body class="image_iframe_body">
      <div class="cke_dialog_title" id="image_iframe_header">画像編集<a href="javascript:cxIframeLayerCallback()" id="header_close" style="float: right; margin-top: 2px;"><img src="{{ asset('/ckeditor/skins/moono-lisa/images/close.png') }}" alt="閉じる"></a></div>
      <form name="image_controll" id="image_controll"
         action="{{ route('admin.wysiwyg.controll') }}"
         method="post" onSubmit="return cxResize()">
        {{ csrf_field() }}
        {{ Form::hidden('image_id', $image_id, ['id' => 'filelink_id'] ) }}
        {{ Form::hidden('image_tm_x1', 0, ['id' => 'image_tm_x1'] ) }}
        {{ Form::hidden('image_tm_y1', 0, ['id' => 'image_tm_y1'] ) }}
        {{ Form::hidden('image_tm_x2', 0, ['id' => 'image_tm_x2'] ) }}
        {{ Form::hidden('image_tm_y2', 0, ['id' => 'image_tm_y2'] ) }}
        {{ Form::hidden('image_tm_width', 0, ['id' => 'image_tm_width'] ) }}
        {{ Form::hidden('image_tm_height',0, ['id' => 'image_tm_height'] ) }}
        {{ Form::hidden('image_edit_mode', '', ['id' => 'image_edit_mode'] ) }}
        {{ Form::hidden('image_edit_id', $edit_id, ['id' => 'image_edit_id'] ) }}
        <input id="image_edit_picture" name="image_edit_picture" type="hidden" value="{{ $image['picture'] }}">
        <div class="image_controll_table" style="margin-left: -8px;">
            <table width="100%" height="307px" border="0" cellspacing="0"
               cellpadding="0" style="border-collapse: collapse;">
               <tr>
                  <td width="100%" align="center" valign="top">
                     <div id="searcharea">
                        <table width="536px" height="472px" align="left" border="0" class="table_search" cellspacing="0" cellpadding="10" bgcolor="#f7f7f7" style="padding: 0px 25px">
                           <tr align="center">
                              <td align="center">
                                 <table width="100%" align="left" valign="top" border="0">
                                    <tr>
                                       <td width="100%" rowspan="2" class="td_col_left">
                                          <table border="0" cellspacing="0" cellpadding="5" align="center"
                                             class="dataTable"
                                             style="border-collapse: collapse; border: solid 1px #CCCCCC;">
                                             <tr>
                                                <th align="center" valign="middle">
                                                   <img src="{{ asset($image['picture'] . '?rnd=' . rand() ) }}" alt="image"
                                                      id="image_picture" class="cropper-hidden">
                                                </th>
                                             </tr>
                                          </table>
                                       </td>
                                       <td align="left" valign="top" border="0" class="td_col_right">
                                          <table width="150px" border="0" cellspacing="0" cellpadding="5"
                                             align="center" valign="top" class="table_full" style="border-collapse: collapse;">
                                             <tr>
                                                <div style="padding: 10px 0px 10px 3px;">
                                                   <label class="cke_dialog_ui_labeled_label label_inline" style="margin-right:12px">サイズ変更</label>
                                                   @php
                                                    $ary_ratio = [
                                                        'free' => '自由',
                                                        '1:1' => '正方形',
                                                        '2:3' => '2:3',
                                                        '3:5' => '3:5',
                                                        '3:4' => '3:4',
                                                        '4:5' => '4:5',
                                                        '5:7' => '5:7',
                                                        '9:16' => '9:16'
                                                    ];
                                                   @endphp
                                                   {{ Form::select('ratio', $ary_ratio, true, ['id' => 'ratio', 'class' => 'applyRatio']) }}
                                                </div>
                                             </tr>
                                             <tr>
                                                <td width="50%"><label class="cke_dialog_ui_labeled_label"
                                                   for="image_rs_x">横幅</label></td>
                                                <td width="50%">
                                                    {{ Form::text('image_rs_x', $image['width'], ['id' => 'image_rs_x', 'class' => 'cke_dialog_ui_input_text', 'style' => 'ime-mode: disabled', 'size' => '3', 'onKeyPress' => "return IsDigit();", 'onChange' => "return cxAspect('image_rs_x', 'image_rs_y', IMAGE_MAX_X, IMAGE_MAX_Y, 0 );", 'onKeyUp' => "return cxAspect('image_rs_x', 'image_rs_y', IMAGE_MAX_X, IMAGE_MAX_Y, 0 );", 'tabindex' => '1']) }}
                                                </td>
                                             </tr>
                                             <tr>
                                                <td width="50%"><label class="cke_dialog_ui_labeled_label"
                                                   for="image_rs_y">高さ</label></td>
                                                <td width="50%">
                                                    {{ Form::text('image_rs_y', $image['height'], ['id' => 'image_rs_y', 'class' => 'cke_dialog_ui_input_text', 'style' => 'ime-mode: disabled', 'size' => '3', 'onKeyPress' => "return IsDigit();", 'onChange' => "return cxAspect('image_rs_x', 'image_rs_y', IMAGE_MAX_X, IMAGE_MAX_Y, 1 );", 'onKeyUp' => "return cxAspect('image_rs_x', '#image_rs_y', IMAGE_MAX_X, IMAGE_MAX_Y, 1 );", 'tabindex' => '2'] ) }}
                                                    </td>
                                             </tr>
                                          </table>
                                          <table width="150px" height="376px" class="table_full">
                                             <tr>
                                                <td>
                                                   <div>
                                                      <a class="cke_dialog_ui_button cke_dialog_ui_button_grey"
                                                         href="javascript:void(0)" onClick="return cxResize()">サイズ変更</a> <br><br>
                                                      <div class="cke_dialog_ui_labeled_label ck_required"
                                                         >※横幅・高さの比率は<br>
                                                         自動で調整されます。
                                                      </div>
                                                   </div>
                                                   <div style="padding: 20px 0px 10px 0px;">
                                                      <a class="cke_dialog_ui_button cke_dialog_ui_button_grey cxTrimming"
                                                         href="javascript:void(0)">切り抜き</a>
                                                      <br> <br>
                                                      <div class="cke_dialog_ui_labeled_label ck_required"
                                                         >※切り抜き範囲は画像を<br>
                                                         ドラッグすることで<br>
                                                         選択できます。
                                                      </div>
                                                   </div>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td valign="top" align="left">
                                                   <div style="padding: 10px 0px 10px 0px;" align="left"
                                                      valign="top"><a href="javascript:void(0)" class="cke_dialog_ui_button cke_dialog_ui_button_grey"
                                                      onClick="return cxCancel()">一つ前に戻す</a>
                                                   </div>
                                                   <div style="padding: 10px 0px 10px 0px;" align="center" class="align_sp"><span
                                                      style="{{ ($rewriteEnable == config('wysiwyg.config.flag_off')) ? 'display:none;' : '' }}"
                                                      align="center">
                                                      {{ Form::checkbox('image_rewrite_flg', config('wysiwyg.config.flag_on'), ($rewriteFlg == config('wysiwyg.config.flag_on')) ? true : false, ['id' => 'image_rewrite_flg', 'onClick' => "return cxRewriteChk('image_rewrite_flg')"]) }}
                                                      &nbsp;<label class="cke_dialog_ui_labeled_label label_inline"
                                                         for="image_rewrite_flg">上書き保存する</label> </span>
                                                   </div>
                                                   <div align="center" class="align_sp"><a
                                                      class="cke_dialog_ui_button cke_dialog_ui_button_padding cke_dialog_ui_button_grey"
                                                      href="javascript:void(0)" onClick="return cxDecision()">決定</a></div>
                                                </td>
                                             </tr>
                                          </table>
                                       </td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                        </table>
                     </div>
                  </td>
               </tr>
            </table>
         </div>
         <!--***ファイル保存先設定レイヤー　ここから***********-->
         <div id="image_reffer" class="layer"
            style="width: 70%;">
            <table>
               <tr>
                  <td width="100%" align="center" valign="top"
                  style="border: solid 1px #a99c9c; background-color: rgb(248,248,248);">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0"
                        class="layerheader">
                        <tr>
                           <td align="left" valign="middle" colspan="2">
                              <div class="cke_dialog_title" id="image_iframe_header" style="width:auto; margin:0px; cursor:unset;">名前を付けて保存<a href="javascript:void(0)" onClick="return cxLayer('#image_reffer',0)" id="header_close" style="float: right; margin-top: 2px;"><img src="{{ asset('/ckeditor/skins/moono-lisa/images/close.png') }}" alt="閉じる"></a></div>
                           </td>
                        </tr>
                     </table>
                        <table width="100%" border="0" cellpadding="5" cellspacing="0"
                           class="dataTable"
                           style="margin:10px">
                           <tr>
                              <th align="left" valign="middle"><label for="filelink_name">画像名称</label>
                                 </td>
                              <td align="left" valign="middle">
                              {{ Form::text('filelink_name', htmlspecialchars($db_value['name']), ['id' => 'filelink_name', 'style' => 'width: 200px;', 'maxlength' => 64]) }}
                             </td>
                           </tr>
                           <tr>
                              <th align="left" valign="middle"><label for="filelink_path">ファイル名</label>
                                 </td>
                              <td align="left" valign="middle">
                                {{ Form::text('filelink_path', htmlspecialchars(substr(basename($db_value['path']), 0, strrpos(basename($db_value['path']), "."))), ['id' => 'filelink_path', 'style' => 'width: 150px; ime-mode: disabled;']) }}
                                {{ Form::hidden('file_ext', substr($db_value['path'], strrpos($db_value['path'], ".")), ['id' => 'file_ext', 'style' => 'width: 150px; ime-mode: disabled;'] ) }}
                                 {{ substr($db_value['path'], strrpos($db_value['path'], ".")) }}
                              </td>
                           </tr>
                        </table>
                     </div>
                     <p style="margin: 10px 0px;"><a href="javascript:void(0)"
                        onClick="return cxRefferSubmit('{{ $image_id }}')"><img
                        src="{{ asset('/ckeditor/images/btn/btn_submit.jpg') }}" alt="決定" width="102"
                        height="21" border="0"></a></p>
                  </td>
               </tr>
            </table>
         </div>
         <!--***ファイル保存先設定レイヤー　ここまで***********-->
      </form>
   </body>
        <script src="{{ asset('/ckeditor/js/library/cropper.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/ckeditor/js/shared.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/ckeditor/gd_files/js/dialog_common.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/com_func.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/image_controll.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/jquery-cropper.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/image_cropper.js') }}" type="text/javascript"></script>
</html>