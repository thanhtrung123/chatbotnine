CKEDITOR.FCKLanguageManager = {
    AvailableLanguages: {
        en: 'English',
        ja: 'Japanese',
    },
    GetActiveLanguage: function() {
        return this.DefaultLanguage;
    },
    TranslateElements: function(A, B, C, D) {
        var e = A.getElementsByTagName(B);
        var E, s;
        for (var i = 0; i < e.length; i++) {
            if ((E = e[i].getAttribute('fckLang'))) {
                if ((s = this.FCKLang[E])) {
                    if (D) s = this.HTMLEncode(s);
                    e[i][C] = s;
                }
            }
        }
    },
    TranslatePage: function(A) {
        this.TranslateElements(A, 'INPUT', 'value');
        this.TranslateElements(A, 'SPAN', 'innerHTML');
        this.TranslateElements(A, 'LABEL', 'innerHTML');
        this.TranslateElements(A, 'OPTION', 'innerHTML', true);
        this.TranslateElements(A, 'LEGEND', 'innerHTML');
    },
    Initialize: function() {
        if (this.AvailableLanguages['ja']) this.DefaultLanguage = 'ja';
        else this.DefaultLanguage = 'en';
        this.ActiveLanguage = {};
        this.ActiveLanguage.Code = this.GetActiveLanguage();
        this.ActiveLanguage.Name = this.AvailableLanguages[this.ActiveLanguage.Code];
        
        document.write( '<script type="text/javascript" src="' + path_link + 'ckeditor/gd_files/lang/ja.js" onerror="alert(\'Error loading \' + this.src);"><\/script>' ) ;
    },
    HTMLEncode: function(A) {
        if (!A) return '';
        A = A.replace(/&/g, '&amp;');
        A = A.replace(/</g, '&lt;');
        A = A.replace(/>/g, '&gt;');
        return A;
    }
};
CKEDITOR.FCKLanguageManager.Initialize();