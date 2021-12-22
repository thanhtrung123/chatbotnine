<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta http-equiv="Content-Style-Type" content="text/css">
      <meta http-equiv="Content-Script-Type" content="text/javascript">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>画像管理</title>
      <link href="{{ asset('/ckeditor/gd_files/css/grid.css') }}" type="text/css" rel="stylesheet">
      <link href="{{ asset('/ckeditor/gd_files/css/dialog.css') }}" type="text/css" rel="stylesheet">
      <script src="{{ asset(mix('js/wysiwyg.js')) }}" type="text/javascript"></script>
      <script type="text/javascript">
         <!--
            var mode = "{{ (isset($_GET['mode']) && $_GET['mode'] == 'library') ? 'library' : '' }}";
            var url_proptype = "{{ route('admin.wysiwyg.property') }}";
            var url_delete = "{{ route('admin.wysiwyg.delete') }}";
            var url_controll = "{{ route('admin.wysiwyg.controll') }}";
            var url_refresh = "{{ route('admin.wysiwyg.refresh') }}";
            var path_link = "{{ asset('/') }}";
            var alert_mess = "{{ (Session::has('success_message')) ? Session::get('success_message') : '' }}";
            if (alert_mess != '') {
                alert(alert_mess);
            }
            var err_msg = "{{ ($errors->has('error_message')) ? $errors->first('error_message') : '' }}";
            if (err_msg != '') {
                alert(err_msg);
            }
         //-->
      </script>
      <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
      <script src="{{ asset('/ckeditor/js/shared.js') }}" type="text/javascript"></script>
      <script  src="{{ asset('/ckeditor/plugins/gd_image/pages/js/image_list.js') }}"  type="text/javascript"></script>
      <script src="{{ asset('/ckeditor/plugins/gd_image/pages/js/com_func.js') }}" type="text/javascript"></script>
      <base target="_self" />
   </head>
   <body class="cke_reset_all">
      <div id="headareaZero">
         <div id="tab" class="cke_dialog_tabs">
            <table border="0" cellspacing="0" cellpadding="0"
               style="border-collapse: collapse;">
               <tr>
                  <td><a href="{{ route('admin.wysiwyg.image', ['mode' => 'upload']) }}" class="cke_dialog_tab" ><span> PCから選んで登録 </span></a></td>
                  <td><a class="cke_dialog_tab cke_dialog_tab_selected" ><span> 登録済画像から選択 </span></a></td>
               </tr>
            </table>
         </div>
         <div class="cke_dialog_contents">
            <form name="image_list" id="image_list"  action="{{ route('admin.wysiwyg.list') }}" method="post">
               <fieldset id="search_fieldset">
               <div class="search_inner">
                  <legend  style="margin-left: 10px;">検索オプション</legend>
                  <div class="lay640">
                     <div class="size2">   
                        <label class="cke_dialog_ui_labeled_label"  for="image_keyword">キーワード</label>
                     </div>
                     <div class="size10 ">
                        {{ Form::text('keyword', htmlspecialchars($search['keyword']), ['id' => 'image_keyword', 'class' => 'cke_dialog_ui_input_text']) }}
                     </div>
                     <div class="size3 action_search"><a href="javascript:void(0)" class="cke_dialog_ui_button cke_dialog_ui_button_padding cke_dialog_ui_button_grey" onClick="return cxSearch()" style="float: right;"
                        tabindex="1">検索</a></div>
                  </div>
                  <div class="lay640">
                     <div class="size2">
                        <label  class="cke_dialog_ui_labeled_label" for="image_update">表示順</label>
                     </div>
                     <div class="size10 suf3">
                        <span class="cke_dialog_ui_labeled_label">更新日の</span>&nbsp; 
                        {{ Form::radio('image_update_datetime', '0', ($search['image_update_datetime'] == config('wysiwyg.config.flag_off')) ? true : false, ['id' => 'upd_asc']) }}
                        <label for="upd_asc"  class="cke_dialog_ui_labeled_label">降順</label>
                        {{ Form::radio('image_update_datetime', '1', ($search['image_update_datetime'] == config('wysiwyg.config.flag_on')) ? true : false, ['id' => 'upd_dsc']) }}
                        <label for="upd_dsc"  class="cke_dialog_ui_labeled_label">昇順</label>
                     </div>
                  </div>
                  <div class="lay640">
                     <div class="size2">
                        <label  class="cke_dialog_ui_labeled_label" for="image_thumbnail">サムネイル</label>
                     </div>
                     <div class="size10 suf3">
                        {!! mkradiobutton(['非表示', '表示'], "image_thumbnail_flg", $search['image_thumbnail_flg'], 0) !!}
                     </div>
                  </div>
                  
               </fieldset>
               {{ Form::hidden('maxrow', $maxRow, ['id' => 'search_maxrow'] ) }}
            </form>
            <div class="lay640">
               <div class="pre12 size4">
                  <span>{!! mkcombobox($search['max_row_list'], "dispNum", $maxRow, "cxDispNum(this.value)") !!}</span>
               </div>
            </div>
            <fieldset>
               <legend>画像ファイルリスト</legend>
               <table width="100%" border="0" cellspacing="0" cellpadding="0"
                  style="margin-top: 0px; margin-bottom: 0px; padding: 1px">
               </table>
               <table width="100%" border="0" cellspacing="0" cellpadding="0"
                  align="center">
                  <tr>
                     <td>
                        <div
                           style="height: 350px; overflow-y: scroll; overflow-x: hidden;"
                           nowrap scope="row">
                           <table width="100%" border="0" cellspacing="0" cellpadding="0"
                              class="dataTable">
                                @foreach ($data_image as $image_item)
                                    @php
                                        $size = getSizeImage(public_path($image_item->file_path));
                                    @endphp
                                    <td style="margin:0px;padding:5px ">
                                            <table border="0" cellspacing="0px" cellpadding="0" margin="0" padding="0" width="100%" style="margin:0px;padding:0px;border-collapse:collapse;">
                                                <tr style="margin:0px;padding:0px;">
                                                    {{ Form::hidden('url_' . $image_item->id, $image_item->file_path, ['id' => 'url_' . $image_item->id] ) }}
                                                    {{ Form::hidden('width_' . $image_item->id, $size['witdh'], ['id' => 'width_' . $image_item->id] ) }}
                                                    {{ Form::hidden('height_' . $image_item->id,  $size['height'], ['id' => 'height_' . $image_item->id] ) }}
                                                    {{ Form::hidden('alt_' . $image_item->id,  htmlDisplay($image_item->file_name), ['id' => 'alt_' . $image_item->id] ) }}
                                                    @if ($search['image_thumbnail_flg'] == config('wysiwyg.config.flag_on'))
                                                        <td width="20%" align="center" style="margin:0px;padding:0px;border-right:solid 1px #999999;">
                                                            <img src="{{ asset($image_item->file_path . '?rnd=' . rand()) }}" width="{{ $size['witdhDisp'] }}" height="{{ $size['heightDisp'] }}" alt="{{ htmlDisplay($image_item->file_name) }}" border="0">
                                                        </td>
                                                    @endif
                                                    <td width="{{ $search['image_thumbnail_flg'] == config('wysiwyg.config.flag_on') ? '70%' : '90%' }}" height="80px" align="left" style="padding-left: 10px;">
                                                        <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%" style="margin:0px;padding:0px;border-collapse:collapse;">
                                                            <tr style="margin:0px;padding:0px;">
                                                                    <td style="margin:0px;padding:0px;padding-left:3px;">
                                                                        <div id="image_name_{{ $image_item->id }}" style="font-weight:bold;font-size:15px;"> {{ htmlDisplay($image_item->file_name) }}</div>
                                                                        {{ htmlDisplay($image_item->file_path) }}<br>
                                                                        <div id="image_update_datetime_{{ $image_item->id }}">更新日：{{ date('Y年m月d日 H時i分s秒', strtotime($image_item->update_at)) }}</div>
                                                                    </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left" valign="middle" style="margin:0px;padding:0px;padding-top:3px;">
                                                                &nbsp;
                                                                <a class="cke_dialog_ui_button cke_dialog_ui_button_padding" href="javascript:void(0)" onclick="cxEdit('{{ $image_item->id }}'); return false;">編集</a>
                                                                &nbsp;
                                                                &nbsp;
                                                                <a class="cke_dialog_ui_button cke_dialog_ui_button_padding" href="javascript:void(0)" onclick="cxDeleteImage('{{ $image_item->id }}', '{{ javaStringEscape(htmlDisplay($image_item->file_name)) }}'); return false;">削除</a>
                                                                &nbsp;
                                                                &nbsp;
                                                                <a href="javascript:void(0)"  class="cke_dialog_ui_button"  onclick="cxShowProperty('{{ $image_item->id }}'); return false;">プロパティ</a>
                                                                &nbsp;
                                                                &nbsp;
                                                                <a href="{{ url($image_item->file_path) }}"  class="cke_dialog_ui_button" target="_blank">プレビュー</a>
                                                                &nbsp;
                                                                &nbsp;
                                                                <a href="javascript:void(0)" style="float: right;" class="cke_dialog_ui_button cke_dialog_ui_button_grey" onclick="cxReturn('{{ $image_item->id }}'); return false;">画像挿入</a>
                                                                &nbsp;
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <hr>
                                        </td>
                                    </tr>
                                @endforeach
                           </table>
                        </div>
                     </td>
                  </tr>
               </table>
               </td>
               </tr>
               </table>
               </td>
               </tr>
               </table>
            </fieldset>
         </div>
         <div class="area-corner-page-count"
            style="width: 97%; margin-bottom: 5px; text-align: center;">
            <span style="float: left; width: 20%;">
            @if ($data_image->currentPage() > 1)
               <a href="javascript:" onClick="return cxPageSet({{$data_image->currentPage() - 1 }})"><img src="{{asset('/ckeditor/images/btn/btn_prev.gif')}}" alt="" width="50" height="16" align="absmiddle" border="0"></a>
            @else
               &nbsp;
            @endif
            </span>
            <span style="float: right; width: 20%;">
            @if ($data_image->currentPage() < $data_image->lastPage())
               <a href="javascript:" onClick="return cxPageSet({{$data_image->currentPage() + 1 }})"><img src="{{asset('/ckeditor/images/btn/btn_next.gif')}}" alt="" width="50" height="16" align="absmiddle" border="0"></a>
            @else
               &nbsp;
            @endif
            </span>
            <span style="width: 60%;">
               @php
                  $startCount = 0;
                  $endCount = 0;
                  if ($data_image->total() > 0) {
                     if (($data_image->currentPage() - 1) > 0) {
                        $startCount = ($data_image->currentPage() - 1) * $data_image->perPage() + 1;
                     } else {
                        $startCount = 1;
                     }
                  }
                  if ($data_image->total() > $data_image->currentPage() * $data_image->perPage()) {
                     $endCount = $data_image->currentPage() * $data_image->perPage();
                  } else {
                     $endCount = $data_image->total();
                  }
               @endphp
               @if ($data_image->total() > 0)
                  {{$data_image->total()}} 件中  {{$startCount}} ～ {{$endCount}} 件表示
               @endif
            </span>
         </div>
         </td>
         </tr>
         </table>
      </div>
        <form name="image_select" id="image_select"
            action="{{ route('admin.wysiwyg.image') }}" method="post">
            {{ Form::hidden('url', '', ['id' => 'url'] ) }}
            {{ csrf_field() }}
            {{ Form::hidden('width', '', ['id' => 'width'] ) }}
            {{ Form::hidden('height', '', ['id' => 'height'] ) }}
            {{ Form::hidden('alt', '', ['id' => 'alt'] ) }}
        </form>
        <form name="page_post" id="page_post" method="post" action="">
            {{ csrf_field() }}
            {{ Form::hidden('page', '') }}
            {{ Form::hidden('maxrow', '', ['id' => 'maxrow'] ) }}
            {{ Form::hidden('keyword', $search['keyword'] ?? '', ['id' => 'image_keyword'] ) }}
            {{ Form::hidden('image_update_datetime', $search['image_update_datetime'], ['id' => 'image_update_datetime'] ) }}
            {{ Form::hidden('image_thumbnail_flg', $search['image_thumbnail_flg'], ['id' => 'image_thumbnail_flg'] ) }}
        </form>
   </body>
</html>