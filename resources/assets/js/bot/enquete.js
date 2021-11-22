(function ($) {
    var mq = window.matchMedia("(max-width: 640px)");

    function appendElement(e) {
        $('.question-check .list_question input[type="radio"]').on('change', function () {
            if ($(this).prop('checked')) {
                if (e.matches) {
                    var section = $(this).closest(".question-check").next().offset().top;
                    $("html, body").animate({scrollTop: section}, 300);
                }
            }
        });
    }

    $(document).ready(function () {
        appendElement(mq);
        mq.addListener(appendElement);
    });
})(jQuery);
