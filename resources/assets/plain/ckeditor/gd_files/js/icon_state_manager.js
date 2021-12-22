for (var ck_instance in CKEDITOR.instances) {
    CKEDITOR.instances[ck_instance].on('selectionChange', function (evt) {
        var tableDressedCommand = this.getCommand('gd_table_property');
        var clearTable = this.getCommand('gd_table_clear');
        this.selTbl = null;
        var selTbl = this.getSelection().getNative() || this.window.$.getSelection() || this.document.$.selection;
        if (!selTbl) {
            tableDressedCommand.disable();
            clearTable.disable();
            return;
        }
        if (selTbl.anchorNode.tagName != undefined && selTbl.anchorNode == selTbl.focusNode && selTbl.focusOffset - selTbl.anchorOffset == 1) {
            selTbl = selTbl.anchorNode.childNodes[selTbl.anchorOffset];
        } else
            selTbl = selTbl.anchorNode;
        while (selTbl.tagName != 'TABLE' && selTbl.tagName != 'BODY') {
            selTbl = selTbl.parentNode;
            if (selTbl.tagName == 'BODY')
                break;
        }
        if (selTbl.tagName == 'BODY') {
            tableDressedCommand.disable();
            clearTable.disable();
            return;
        }
        tableDressedCommand.enable();
        clearTable.enable();
        this.selTbl = selTbl;
    });
// CKEditor Filter
    CKEDITOR.on('instanceReady', function (ev)
    {
        ev.editor.filter.addTransformations([
            [
                'img{width,height}: sizeToAttribute',
            ],
            [
                'table: sizeToAttribute',
            ],
        ]);
    });
    CKEDITOR.instances[ck_instance].on('paste', function (evt) {
        if (!window.lastDropRange) {
            evt.stop();
            CKEDITOR.CleanWord(evt.data.dataValue);
            // CKEDITOR.currentInstance.getSelection().selectRanges([window.lastDropRange]);
            // window.lastDropRange = null;
        }
        window.lastDropRange = null;
    });
    CKEDITOR.instances[ck_instance].on('drop', function (evt) {
        window.lastDropRange = evt.data.dropRange;
    });
    CKEDITOR.instances[ck_instance].on('drop', function (evt) {
        window.lastDropRange = evt.data.dropRange;
    });

// Make CKEditor respect headarea when in maximized state
    CKEDITOR.instances[ck_instance].on('afterCommandExec', function (evt) {
// if maximized state = 1
        if (evt.data.name == 'maximize' && this.getCommand("maximize").state == 1) {
            var maximizedHeight = this.ui.space('contents').getStyle('height');
            var maximizedWidth = this.ui.space('contents').getStyle('width');
            if (document.getElementById('headareaZero') != null) {
                var headareaHeight = document.getElementById('headareaZero').offsetHeight;
            } else if (document.getElementById('headarea') != null) {
                var headareaHeight = document.getElementById('headarea').offsetHeight;
            } else {
                return;
            }

            // maximized CKEditor will be in div element with class cke_maximized
            // set it css with prototype, letter may be require change to jquery
            var divMaximized = $$('.cke_maximized')[0];
            divMaximized.setStyle({
                // should be bellow headarea to show correct dropdown elements of headareapush
                'z-index': '1',
                top: headareaHeight + 'px',
            });
            // (width, height)
            this.resize(0, -headareaHeight);
        }
    });
}