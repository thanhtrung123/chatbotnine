/*
 * 画像プロパティ
 */
if (!window.cxIframeLayerCallback) {
    window.cxIframeLayerCallback = window.frameElement.cxIframeLayerCallback;
}

// プロパティ設定後閉じる
window.onload = function() {
    //モードが1なら、returnする
    if (submitFlg && submitFlg == 1) {
        setTimeout(function() { 
            alert('画像名称を変更しました');
            // 親画面に値を渡す
            cxIframeLayerCallback(retObj);
        }, 100);
    }
}

// 設定ボタン押下後入力チェック、サブミット
function cxSubmit() {
    // ファイル名称チェック
    if ($('#image_name').val() == "") {
        // 必須
        alert("画像名称を入力してください。");
        $('#image_name').focus();
        return false;
    }
    document.image_property.submit();
    return false;

}
