CKEDITOR.plugins.add('gd_image', {
    lang: "en,ja",
    init: function (editor) {

        editor.addCommand('gd_image', new CKEDITOR.dialogCommand('gd_image'));
        editor.ui.addButton('GdImage', {
            icon: this.path + 'icons/image.png',
            label: editor.lang.gd_image.button,
            command: 'gd_image',
        });
        CKEDITOR.dialog.add('gd_image', this.path + 'dialogs/image.js')

        editor.addMenuGroup('imgGroup');

        editor.addMenuItems && editor.addMenuItems({
            gd_image_context: {
                label: editor.lang.gd_image.button,
                icon: this.path + 'icons/image.png',
                command: "gd_image",
                group: 'imgGroup',
                order: 1
            }
        });

        editor.on("doubleclick", function (b) {
            var a = b.data.element;
            !a.is("img") || a.data("cke-realelement") || a.isReadOnly() || (b.data.dialog = "gd_image")
        });

        editor.contextMenu.addListener(function (element) {
            if (element.getAscendant('img', true)) {
                return {
                    gd_image_context: CKEDITOR.TRISTATE_OFF
                };
            }
        });
    }
});
