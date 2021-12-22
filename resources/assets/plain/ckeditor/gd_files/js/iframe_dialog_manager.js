function show_iframe_dialog(editor, href, width, height) {
    // hide default dialog before process
    var ck_dialog = CKEDITOR.dialog.getCurrent().parts.dialog;
    $(ck_dialog).hide();
    // CKEDITOR.dialog.getCurrent().parts.dialog.$.hide();
    // hide default link dialog
    var tbl = CKEDITOR.dialog.getCurrent().parts.contents.$.parentNode.parentNode.parentNode;
    $(tbl).hide();
    // add frame
    var frm = null;
    if (tbl.parentNode.getElementsByTagName('iframe').length > 0) {
        frm = tbl.parentNode.getElementsByTagName('iframe')[0];
    }
    else {
        frm = editor.document.createElement('iframe').$;
        tbl.parentNode.appendChild(frm);
        frm.style.width = width + 'px';
        frm.style.height = height + 'px';
    }
    $(frm).attr('id', 'wysiwyg_upload_image');
    frm.contentDocument.location.href = href;
    // show data when frame is loaded
    frm.onload = function (e) {
        var ck_dialog_show =  CKEDITOR.dialog.getCurrent().parts.dialog;
        $(ck_dialog_show).show();
        // close dialog when validate fail
        if (!validate_dialog(this))
            $(CKEDITOR.dialog.getCurrent().parts.close).click();
            // CKEDITOR.dialog.getCurrent().parts.close.$.click();
    };
}

function validate_dialog(frm) {
    var dialog_name = CKEDITOR.dialog.getCurrent().getName(),
        ret = true;
    // check link dialog
    if (dialog_name == 'gd_link_dialog' && frm.contentWindow.oLinkChecked == false) ret = false;
    
    return ret;
}