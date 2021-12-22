(function($){
$.GDITFUNC = {};
$.GDITFUNC.frameAddClass = function(options) { //v1.0
    var defaults = {
        //Default Options
        arrayBr: [],
        frameID: ''
        },
        s = $.extend(defaults, options);
    //if (!(s.bg && s.bg.length)) return { active: false }
    var arrayBr = s.arrayBr,
        firstLoad = false;
    /*---- INIT ----*/
    function init() {
        readFrameLoad();
        $(window).on('resize', function(){{
            readFrameResize();
        }});
    };
    function readFrameLoad() {
        function frameFindisplay() {
            var iframe = document.getElementById(s.frameID);
            if(iframe && iframe.contentWindow.document.body && iframe.contentWindow.document.body.classList) {
                iframe.addEventListener('load',function(){
                    addClass(iframe);
                })
            }else {
                window.requestAnimationFrame(frameFindisplay);
            }
        };
        window.requestAnimationFrame(frameFindisplay);
    }
    function readFrameResize(){
        function frameFindisplay() {
            var iframe = document.getElementById(s.frameID);
            if(iframe && iframe.contentWindow.document.body && iframe.contentWindow.document.body.classList) {
                addClass(iframe);
            }else {
                window.requestAnimationFrame(frameFindisplay);
            }
        };
        window.requestAnimationFrame(frameFindisplay);
    }
    function addClass(iframe) {
        var frameW = jQuery(window).width();
        arrayBr.forEach(function(value) {
            if(frameW < value) {
                iframe.contentWindow.document.body.classList.add('bp'+value)
            }else {
                iframe.contentWindow.document.body.classList.remove('bp'+value)
            }
        });
    }
    return {
        init: init
    }
};
var frame_img = new $.GDITFUNC.frameAddClass({
    arrayBr: [720,590,490,350],
    frameID: 'image_iframe',
});
var wysiwyg_upload_image = new $.GDITFUNC.frameAddClass({
    arrayBr: [720,590,490,350],
    frameID: 'wysiwyg_upload_image',
});
$(document).on('click','#cke_16',function(){
    function checkframeload() {
        var iframeLoad = document.getElementById('wysiwyg_upload_image');
        if(iframeLoad && $(iframeLoad.contentWindow.document.body).find('#headareaZero').length) {
            iframeLoad.addEventListener('load',function(){
                var clickTab = $(iframeLoad.contentWindow.document.body).find('#headareaZero');
                var clickBtn = $(iframeLoad.contentWindow.document.body).find('.cke_dialog_ui_button');
                $(clickTab).find('.cke_dialog_tab').each(function(i){
                    $(this).on('click',function(){
                        checkframeload();
                        wysiwyg_upload_image.init();
                    });

                });
                $(clickBtn).each(function(i){
                    $(this).on('click',function(){
                        frame_img.init();
                    });
                })
            });
        }else {
            window.requestAnimationFrame(checkframeload);
        }
    }
    window.requestAnimationFrame(checkframeload);
});
$(window).on('resize',function(){
    var iframe = document.getElementById('image_iframe');
    if(iframe) {
        var divCover = iframe.previousSibling;
        divCover.style.width = $(window).width()+'px';
        divCover.style.height= $(window).height()+'px';
        if($(window).width() > 720) {
            iframe.style.left = ($(window).width() - $(iframe).width())/2+'px';
        }
        if ($(window).height() > $(iframe).height()) {
            iframe.style.top = ($(window).height() - $(iframe).height())/2+'px';
        } else iframe.style.top = 0;
    }
})
})(jQuery);
