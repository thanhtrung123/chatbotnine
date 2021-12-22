<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="robots" content="noindex, nofollow">
      <meta http-equiv="Content-Style-Type" content="text/css">
      <meta http-equiv="Content-Script-Type" content="text/javascript">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>画像アップロード</title>
      <link rel="stylesheet" href="{{ asset('/ckeditor/gd_files/css/dialog.css') }}" type="text/css">
      <script src="{{ asset(mix('js/wysiwyg.js')) }}"></script>
      <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
      <script src="{{ asset('/ckeditor/js/shared.js') }}" type="text/javascript"></script>
      <script src="{{ asset('/ckeditor/gd_files/js/dialog_common.js') }}" type="text/javascript"></script>
      <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/com_func.js') }}" type="text/javascript"></script>
      <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/image.js') }}" type="text/javascript"></script>
      <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/image_upload.js') }}" type="text/javascript"></script>
      <script type="text/javascript">
        var checkImage = "{{route('admin.wysiwyg.image_check', ['_token' => csrf_token()])}}";
        var get_mode = "{{ (isset($_GET['mode']) && $_GET['mode'] == 'upload') ? 'upload' : 'property' }}";
        var err_msg = "{{ ($errors->has('error_message')) ? $errors->first('error_message') : '' }}";
        var ALLOWED_EXTENSIONS_IMAGE = "{{ implode(',', config('wysiwyg.config.type_file_image')) }}";
        var POST = new Object();
        POST["url"] = "{{ isset($_POST['url']) ? javaStringEscape(asset($_POST['url'])) : '' }}";
        POST["width"] = "{{ isset($_POST['width']) ? javaStringEscape($_POST['width']) : '' }}";
        POST["height"] = "{{ isset($_POST['height']) ? javaStringEscape($_POST['height']) : '' }}";
        POST["alt"] = "{{ isset($_POST['alt']) ? javaStringEscape($_POST['alt']) : '' }}";
    </script>
   </head>
   <body>
      <div id="headareaZero" style="margin-bottom: 0px !important">
         <div id="divProperty" style="display: none;">
            <div id="tab" class="cke_dialog_tabs">
               <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                  <tbody>
                     <tr>
                        <td><a href="{{ route('admin.wysiwyg.image', ['mode' => 'upload']) }}" class="cke_dialog_tab"><span style="font: normal normal normal 12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif;"> PCから選んで登録 </span></a></td>
                        <td><a class="cke_dialog_tab cke_dialog_tab_selected"><span style="font: normal normal normal 12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif;"> 登録済画像から選択 </span></a></td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <table width="100%" height="520px" cellspacing="0" cellpadding="5" class="cke_dialog_contents"
             style="background-color: #FFFFFF; border-top: solid 1px #999999;">
                <tr>
                    <td valign="top">
                        <div style="border-collapse: collapse; text-align: left; padding: 15px 2px 10px 2px; align: center">
                            <form name="image_property" id="image_property"
                                action="javascript:void(0)" method="post"
                                enctype="multipart/form-data"
                                onsubmit="cxSubmit_Property(); return false;">
                                <table width="100%" border="0" cellspacing="0" align="center"
                                    style="border: solid 1px #999999;">
                                    <tr>
                                        <td width="30%" align="center" valign="top" rowspan="3"
                                            style="padding-top: 20px;"><img id="imgPreview"
                                                                            src="{{ asset('/ckeditor/images/spacer.gif') }}" alt=""
                                                                            width="0"
                                                                            height="0"></td>
                                        <td width="70%" align="center" valign="top"
                                            style="border-left: solid 1px #999999; border-bottom: solid 1px #999999; padding-left: 5px; padding-top: 5px; padding-bottom: 5px;"
                                            nowrap>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr style="padding-top: 10px;">
                                                    <td
                                                            style="font-size: 15px; font-weight: bold;"><span
                                                                class="cke_dialog_ui_labeled_label"
                                                                fckLang="DlgImgAlt">Short Description</span></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ Form::text('txtAlt', '', ['id' => 'txtAlt', 'class' => 'cke_dialog_ui_input_text', 'style' => "WIDTH: 90%", 'maxlength' => "64"]) }}
                                                    </td>
                                                </tr>
                                                <tr style="padding-bottom: 10px;">
                                                    <td style="padding-left: 1px;">
                                                        {{ Form::checkbox('chkAlt', '', false, ['id' => 'chkAlt', 'onClick' => "setAlt();"]) }}
                                                        <label for="chkAlt"><span class="cke_dialog_ui_labeled_label" fckLang="DlgImgAlt_Check">CheckBox Label</span></label>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-left: solid 1px #999999; border-bottom: solid 1px #999999; margin: 0px; padding-left: 5px; padding-top: 5px; padding-bottom: 5px;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr style="padding-left: 5px;">
                                                    <td style="width: 20px;"><label for="txtWidth"><span
                                                                    class="cke_dialog_ui_labeled_label"
                                                                    fckLang="DlgImgWidth">Width</span>&nbsp;</label></td>
                                                    <td style="width: 50px;">
                                                        {{ Form::text('txtWidth', '', ['id' => 'txtWidth', 'class' => 'cke_dialog_ui_input_text', 'style' => "width: 50px;ime-mode: disabled;", 'onkeyup' => "OnSizeChanged('WIDTH');", 'onkeypress' => "return IsDigit();"]) }}
                                                    </td>
                                                    <td nowrap rowspan="2" style="width: 20px;">
                                                        <div id="btnLockSizes" class="BtnLocked"
                                                            onmouseover="this.className = (bLockRatio ? 'BtnLocked' : 'BtnUnlocked' ) + ' BtnOver';"
                                                            onmouseout="this.className = (bLockRatio ? 'BtnLocked' : 'BtnUnlocked' );"
                                                            title="Size Locked" onclick="SwitchLock(this);"></div>
                                                    </td>
                                                    <td nowrap rowspan="2">
                                                        <div id="btnResetSize" class="BtnReset"
                                                            onmouseover="this.className='BtnReset BtnOver';"
                                                            onmouseout="this.className='BtnReset';" title="Size Reset"
                                                            onclick="ResetSizes();"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20px;"><label for="txtHeight"><span
                                                                    class="cke_dialog_ui_labeled_label"
                                                                    fckLang="DlgImgHeight">Height</span>&nbsp;</label></td>
                                                    <td>
                                                    {{ Form::text('txtHeight', '', ['id' => 'txtHeight', 'class' => 'cke_dialog_ui_input_text', 'style' => "width: 50px;ime-mode: disabled;", 'onkeyup' => "OnSizeChanged('HEIGHT');", 'onkeypress' => "return IsDigit();"]) }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-left: solid 1px #999999; padding-left: 5px; padding-top: 5px; padding-bottom: 5px;">
                                            {{ Form::hidden('cmbAlign', '', ['id' => 'cmbAlign'] ) }}
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                style="margin: 0px;">
                                                <tr style="padding-top: 5px; padding-bottom: 5px;">
                                                    <td align="left" valign="top"><span
                                                                fckLang="DlgImgAlign"
                                                                class="cke_dialog_ui_labeled_label">Image Align</span></br>
                                                    </td>
                                                </tr>
                                                <tr style="padding-top: 5px; padding-left: 5px;">
                                                    <td width="120" align="left" valign="top"
                                                        style="padding-left: 10px;"><a href="#" onClick="alignSet('')"><img
                                                                    src="{{ asset('/ckeditor/gd_files/image/icon_normal.gif') }}" alt=""
                                                                    width="80" height="40"
                                                                    border="0"></a>
                                                        <div style="margin: 5px 0px;">
                                                        {{ Form::radio('align', '', false, ['id' => 'align01', 'onClick' => 'alignSet(this.value)']) }}
                                                            <label
                                                                    for="align01"><span fckLang="DlgImgAlignNo"
                                                                                        class="cke_dialog_ui_labeled_label">No Specification</span></label>
                                                        </div>
                                                    </td>
                                                    <td width="120" align="left" valign="top"
                                                        style="padding-left: 10px;"><a href="#"
                                                                                    onClick="alignSet('left')"><img
                                                                    src="{{ asset('/ckeditor/gd_files/image/icon_left.gif') }}"
                                                                    alt="" width="80" height="40" border="0"></a>
                                                        <div style="margin: 5px 0px;">
                                                        {{ Form::radio('align', 'left', false, ['id' => 'align02', 'onClick' => 'alignSet(this.value)']) }}
                                                            <label
                                                                    for="align02"><span fckLang="DlgImgAlignLeft"
                                                                                        class="cke_dialog_ui_labeled_label">Left</span></label>
                                                        </div>
                                                    </td>
                                                    <td width="120" align="left" valign="top"
                                                        style="padding-left: 10px;"><a href="#"
                                                                                    onClick="alignSet('right')"><img
                                                                    src="{{ asset('/ckeditor/gd_files/image/icon_right.gif') }}"
                                                                    alt="" width="80" height="40" border="0"></a>
                                                        <div style="margin: 5px 0px;">
                                                        {{ Form::radio('align', 'right', false, ['id' => 'align03', 'onClick' => 'alignSet(this.value)']) }}
                                                            <label
                                                                    for="align03"><span fckLang="DlgImgAlignRight"
                                                                                        class="cke_dialog_ui_labeled_label">Right</span></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="padding-top: 5px; padding-bottom: 10px;">
                                                    <td width="120" align="left" valign="top"
                                                        style="padding-left: 10px;"><a href="#"
                                                                                    onClick="alignSet('middle')"><img
                                                                    src="{{ asset('/ckeditor/gd_files/image/icon_center.gif') }}"
                                                                    alt="" width="80" height="40" border="0"></a>
                                                        <div style="margin: 5px 0px;">
                                                        {{ Form::radio('align', 'middle', false, ['id' => 'align04', 'onClick' => 'alignSet(this.value)']) }}
                                                            <label
                                                                    for="align04"><span fckLang="DlgImgAlignMiddle"
                                                                                        class="cke_dialog_ui_labeled_label">Middle</span></label>
                                                        </div>
                                                    </td>
                                                    <td width="120" align="left" valign="top"
                                                        style="padding-left: 10px;"><a href="#"
                                                                                    onClick="alignSet('top')"><img
                                                                    src="{{ asset('/ckeditor/gd_files/image/icon_top.gif') }}"
                                                                    alt="" width="80" height="40" border="0"></a>
                                                        <div style="margin: 5px 0px;">
                                                        {{ Form::radio('align', 'top', false, ['id' => 'align05', 'onClick' => 'alignSet(this.value)']) }}
                                                            <label
                                                                    for="align05"><span fckLang="DlgImgAlignTop"
                                                                                        class="cke_dialog_ui_labeled_label">Top</span></label>
                                                        </div>
                                                    </td>
                                                    <td width="120" align="left" valign="top"
                                                        style="padding-left: 10px;"><a href="#"
                                                                                    onClick="alignSet('bottom')"><img
                                                                    src="{{ asset('/ckeditor/gd_files/image/icon_bottom.gif') }}"
                                                                    alt="" width="80" height="40" border="0"></a>
                                                        <div style="margin: 5px 0px;">
                                                        {{ Form::radio('align', 'bottom', false, ['id' => 'align06', 'onClick' => 'alignSet(this.value)']) }}
                                                            <label
                                                                    for="align06"><span fckLang="DlgImgAlignBottom"
                                                                                        class="cke_dialog_ui_labeled_label">Bottom</span></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <div align="center" style="margin: 15px 0px;"><input type="submit"
                                                                                    name="submit_property"
                                                                                    id="submit_property"
                                                                                    value="設定"
                                                                                    class="cke_dialog_ui_button cke_dialog_ui_button_padding cke_dialog_ui_button_grey">
                                    &nbsp; &nbsp; &nbsp; <a href="{{ route('admin.wysiwyg.list') }}" class="cke_dialog_ui_button">一覧画面へ</a></div>
                            </form>
                    </td>
                </tr>
        </table>
         </div>
         <div id="divUpload" style="DISPLAY: none">
            <div id="tab" class="cke_dialog_tabs">
               <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                  <tbody>
                     <tr>
                        <td><a href="{{ route('admin.wysiwyg.image', ['mode' => 'upload']) }}" class="cke_dialog_tab cke_dialog_tab_selected"><span style="font: normal normal normal 12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif;"> PCから選んで登録 </span></a>
                        </td>
                        <td><a class="cke_dialog_tab" href="{{ route('admin.wysiwyg.list') }}"><span style="font: normal normal normal 12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif;"> 登録済画像から選択 </span></a></td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <table width="100%" height="510px" cellspacing="0" cellpadding="0" class="cke_dialog_contents">
               <tbody>
                  <tr>
                     <td valign="top">
                        <form name="image_upload" id="image_upload" action="{{ route('admin.wysiwyg.upload') }}" method="post" enctype="multipart/form-data" onsubmit="cxSubmit_Upload('{{ public_path(env('QA_IMAGES_PATH')) }}'); return false;">
                            {{ csrf_field() }}
                                @for ($i = 0; $i < 5; $i++)
                                    <table width="90%" border="0" cellspacing="0" cellpadding="0" align="center" class="dataTable" style="border-collapse:collapse;margin-top: 10px;">
                                        <tr>
                                            <th width="15%" align="left" valign="top" class="cke_dialog_ui_labeled_label">画像名称{{ $i + 1 }}</th>
                                                <td width="85%" valign="top">
                                                    {{ Form::form_text('image_name_' . $i, (isset($POST['image_name_' . $i]) ? htmlDisplay($POST['image_name_' . $i]) : ""), TRUE, ['style' => 'WIDTH: 98%;', 'class' => 'cke_dialog_ui_input_text', 'maxlength' => '64', 'id' => 'image_name_' . $i] ) }}
                                                </td>
                                        </tr>
                                        <tr>
                                            <th width="15%" align="left" valign="top" class="cke_dialog_ui_labeled_label">ファイル{{ $i + 1 }}</th>
                                                <td width="85%">
                                                    <label width="10%" for="image_path_{{ $i }}" class="cke_dialog_ui_button cke_dialog_ui_button_padding" style="margin-left: 1px;">参照</label> <span type="text" id="file-selected_{{ $i }}"></span>
                                                </td>
                                                {{ Form::form_file('image_path_' . $i, '', '', ['style' => 'visibility:hidden; display: none;', 'id' => 'image_path_' . $i] ) }}
                                            <script> 
                                                var filetype_{{ $i }} = document.getElementById("image_path_{{ $i }}"); 
                                                filetype_{{ $i }}.onchange = function() {
                                                    var fileName = ""; 
                                                    fileName = filetype_{{ $i }}.value;
                                                    document.getElementById("file-selected_{{ $i }}").innerHTML = fileName.replace(/^.*[\\\/]/, ""); 
                                                };
                                            </script>
                                        </tr>
                                    </table>
                                    {{ Form::hidden('rename_file_path_' . $i, '', ['id' => 'rename_file_path_' . $i] ) }}
                                @endfor
                            <br>
                           <br>
                           <div align="center">
                           <input type="submit" class="cke_dialog_ui_button cke_dialog_ui_button_grey" name="submit_upload" id="submit_upload" style="padding-right: 30px; padding-left: 30px;" value="登録"></div>
                        </form>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </body>
</html>