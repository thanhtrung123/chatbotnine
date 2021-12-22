CKEDITOR.dialog.add('gd_image', function (editor) {
    return {
        title: editor.lang.gd_image.title,
        minWidth:       625,
        minHeight:      625,
        contents: [{
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [{
                        type: 'text',
                        id: 'src',
                        label: 'Source',
                        validate: CKEDITOR.dialog.validate.notEmpty("Image source field cannot be empty"),
                    }, {
                        type: 'text',
                        id: 'alt',
                        label: 'Alternative'
                    }
                ]
            }
        ],
        onShow: function () {
            var href = wysiwyg_image;
            show_iframe_dialog(editor, href, '680', '680');
        },
        onOk: function () {
        }
    }
});