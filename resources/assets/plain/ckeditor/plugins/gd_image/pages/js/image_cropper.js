$(function () {
    var URL = window.URL || window.webkitURL;
    var $image = $('#image_picture');
    var originalImageURL = $image.attr('src');
    var x1 = document.getElementById('image_tm_x1');
    var y1 = document.getElementById('image_tm_y1');
    var x2 = document.getElementById('image_tm_x2');
    var y2 = document.getElementById('image_tm_y2');

    var dataHeight = document.getElementById('image_tm_height');
    var dataWidth = document.getElementById('image_tm_width');
    var options = {
        responsive : false,
        background : false,
        autoCrop : false,
        movable : false,
        cropBoxResizable: true,
        guides: false,
        zoomable: false,
        zoomOnTouch: false,
        zoomOnWheel: false,
        viewMode: "1",
        crop: function (e) {
            var data = e.detail;
            x1.value = Math.round(data.x);
            y1.value = Math.round(data.y);
            dataHeight.value = Math.round(data.height);
            dataWidth.value = Math.round(data.width);
            x2.value = Math.round(data.x) + Math.round(data.width);
            y2.value = Math.round(data.y) + Math.round(data.height);
        }
    };
    // Cropper
    $image.cropper(options);

    $(document).on('click', '.cxTrimming', function () {
        if ($image.data('cropper')) {
            $image.cropper('destroy').cropper(options);
        }
        // モードの設定
        $('#image_edit_mode').val(TRIMMING_MODE);
        // -- トリミングに必要な情報の取得 -- //
        var imageX1 =$('#image_tm_x1').val();
        var imageY1 = $('#image_tm_y1').val();
        var imageX2 = $('#image_tm_x2').val();
        var imageY2 = $('#image_tm_y2').val();
        var imageWidth = $('#image_tm_width').val();
        var imageHeight = $('#image_tm_height').val();

        // 不正サイズチェック
        if ( Number(imageWidth) == 0 || Number(imageHeight) == 0 ) {
            alert('切り抜きのサイズが不正です。\n画像上で切り抜きする範囲を指定してください。');
            return false;
        }

        // -- サブミット -- //
        document.image_controll.submit();
        return false;
    });

    $(document).on('change', '.applyRatio', function () {
        var r = document.getElementById("ratio");
        var selectedValue = r.options[r.selectedIndex].value;
        if ( selectedValue == "free" ) {
            options['aspectRatio'] = 'NaN';
        } else {
            var slv = selectedValue.split(":");
            options['aspectRatio'] = slv[0] / slv[1];
        }
        $image.cropper('destroy').cropper(options);
    });
});