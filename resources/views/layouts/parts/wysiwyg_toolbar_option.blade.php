<script>
    var wysiwyg_image = "{{ route('admin.wysiwyg.image') }}";
    var skin = "{{ config('wysiwyg.config.skin') }}";
    var uiColor = "{{ config('wysiwyg.config.uiColor') }}";
    var lang = "{{ config('wysiwyg.config.language') }}";
    var height = "{{ config('wysiwyg.config.height') }}";
    var options_toolbar = @json($options_toolbar_enabled);
    var toolbar = [];
    for (var key in options_toolbar) {
        if (options_toolbar.hasOwnProperty(key)) {
            toolbar.push({
                name : key,
                items : options_toolbar[key],
            })
        }
    }
    CKEDITOR.disableAutoInline = true;
    CKEDITOR.replace( 'answer', {
        height : height,
        language: lang,
        uiColor: uiColor,
        skin: skin,
        extraPlugins: 'widget,gd_image',
        removePlugins: 'image',
        // stylesSet: 'ai',
        contentsCss: [path_link + 'ckeditor/upload/editcss/2.css', path_link + 'ckeditor/gd_files/css/internal.css'],
        format_tags: 'p;h2;h3;h4;h5;h6',
        youtube_noembed : true,
        TableWidth : 100, //テーブル幅（単位：％）
        TableBorder : 1, //テーブル枠	[0|1|3|5]
        TablePadding : 5, //セル余白
        TableSpacing : 0, //セル間隔
        TableCellSpacing : 0,
        Mobile : false,
        toolbar : toolbar,
        allowedContent : true
    });
</script>