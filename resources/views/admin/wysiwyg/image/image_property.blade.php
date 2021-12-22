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
      <title>画像プロパティ</title>
      <link rel="stylesheet" href="{{ asset('/ckeditor/gd_files/css/dialog.css') }}" type="text/css">
      <script src="{{ asset(mix('js/wysiwyg.js')) }}"></script>
      <script src="{{ asset('/ckeditor/js/shared.js') }}" type="text/javascript"></script>
      <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/image_property.js') }}" type="text/javascript"></script>
      <script type="text/javascript">
            @php
                $submitFlg = 0;
                //Remove session flg
                if (session()->has('submit_flg')) {
                    $submitFlg = session()->get('submit_flg');
                }
            @endphp
            // 決定フラグ
            var submitFlg = "{{ $submitFlg }}";
            var err_msg = "{{ ($errors->has('error_message')) ? $errors->first('error_message') : '' }}";
            if (err_msg != '') {
                alert(err_msg);
            }
            // 戻値
            var retObj = new Object();
            retObj["id"]              = "{{ $property['id'] }}";
            retObj["name"]            = "{{ htmlspecialchars(javaStringEscape($property['file_name'])) }}";
            retObj["update_datetime"] = "{{ date('Y年m月d日 H時i分s秒', strtotime($property['update_at'])) }}";
      </script>
      <base target="_self" />
   </head>
   <body class="image_iframe_body">
      <div class="cke_dialog_title" id="image_iframe_header">画像プロパティ<a href="javascript:cxIframeLayerCallback()" id="header_close" style="float: right; margin-top: 2px;">
      <img src="{{ asset('/ckeditor/skins/moono-lisa/images/close.png') }}" alt="閉じる"></a></div>
      <form name="image_property" id="image_property" action="{{ route('admin.wysiwyg.property_save', ['id' => $property['id']]) }}" method="post" onsubmit="return cxSubmit()" style="margin-top: 10px;">
        {{ csrf_field() }}
        {{ Form::hidden('image_id', $property['id'], ['id' => 'image_id'] ) }}
        <table border="0" cellspacing="0" cellpadding="5" width="100%" style="padding:10px; border: 1px solid">
           <tr>
                <td width="30%">
                    <label class="cke_dialog_ui_labeled_label" for="image_name">画像名称</label>
                </td>
                <td width="70%">
                    {{ Form::text('image_name', $property['file_name'], ['id' => 'image_name', 'class' => 'cke_dialog_ui_input_text', 'style' => 'width:200px;', 'maxlength' => '64']) }}
                </td>
           </tr>
           <tr>
              <td width="30%"><label class="cke_dialog_ui_labeled_label" for="image_path">ファイルパス</label></td>
              <td width="70%">{{ htmlDisplay($property['file_path']) }}</td>
           </tr>
           <tr>
              <td width="30%"><label class="cke_dialog_ui_labeled_label" for="image_regist">登録日</label></td>
              <td width="70%">{{ date('Y年m月d日 H時i分s秒', strtotime($property['post_at'])) }}</td>
           </tr>
           <tr>
              <td width="30%"><label class="cke_dialog_ui_labeled_label" for="image_update">更新日</label></td>
              <td width="70%">{{ date('Y年m月d日 H時i分s秒', strtotime($property['update_at'])) }}</td>
           </tr>
           <tr>
                <td></td><td style="float: right;">
                <a href="javascript:void(0)" onClick="return cxSubmit()" class="cke_dialog_ui_button cke_dialog_ui_button_grey cke_dialog_ui_button_padding" tabindex ="1">設定</a>&nbsp;&nbsp;
                <a onClick="cxIframeLayerCallback();"  class="cke_dialog_ui_button" id="header_close">キャンセル</a>
                </td>
            <tr>
        </table>
      </form>
   </body>
</html>