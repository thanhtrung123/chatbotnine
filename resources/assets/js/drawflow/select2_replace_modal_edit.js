/**
 * Replace special characters
 * @param {string} s
 */
function htmlEnc(s) {
    return s.replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/'/g, '&#39;')
    .replace(/"/g, '&#34;');
}

/**
 * Remove element
 * @param {array} arr
 */
function removeA(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}

/**
 * Create option select2 keyword
 * @param elem_select
 * @param val_select
 * @param all_keywords
 */
function createOptionSelectKeyWordAll(elem_select, val_select, all_keywords) {
    all_keywords = Object.keys(all_keywords);
    for (let index = 0; index < val_select.length; index++) {
        if (all_keywords.includes(val_select[index])) {
            removeA(all_keywords, val_select[index]);
        }
    }
    var data_option = all_keywords.concat(val_select);
    $(elem_select).empty();
    data_option.map(function (value) {
        $(elem_select).append(`<option value="${htmlEnc(value)}">${htmlEnc(value)}</option>`);
    })
    $(elem_select).val(val_select).trigger('change');
}

/**
 * Loop keywords
 */
Object.keys(keywords).map(function (item) {
    var div_dropdown_select2 = $('#dropdown_select2');
    if (typeof getFullscreenElement() !== 'undefined') {
        div_dropdown_select2 = $('#mySelect2');
    }
    $('#select' + item).select2({
        dropdownParent: $(div_dropdown_select2),
        createTag: function (params) {
            var term = $.trim(params.term);
            var options_selected = $('#select' + item).val();
            if (term === '' || options_selected.indexOf(term) > -1) {
                $('#select' + item).next().find('input.select2-search__field').val('');
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // add additional parameters
            }
        },
        allowClear: false,
        tags: true,
        selectOnClose: false,
        debug: true,
        tokenSeparators: [' '],
        matcher: function matchCustom(params, data) {
            if (oldParams != params.term) {
                oldParams = params.term;
                modifiedData = {};
            }
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1 && !data.selected) {
                modifiedData = $.extend({}, data, true);
                return modifiedData;
            }
            if (data.text.toLowerCase() == params.term.toLowerCase()) {
                dataSame =$.extend({}, data, true);
                return dataSame;
            }
            return true;
        }
    });
    createOptionSelectKeyWordAll('#select' + item, keywords[item], all_keywords);
    var allowClear = $('<span class="select2-selection__clear">×</span>');
        if ($('#select' + item).next().find('span.select2-selection__clear').length == 0 && $('#select' + item).val().length) {
        $('#select' + item).next().find('.select2-selection__rendered').prepend(allowClear);
    }
})

/**
 * Get full screen
 */
function getFullscreenElement () {
    if (document.webkitFullscreenElement) {
        return document.webkitFullscreenElement;
    }
    else if (document.mozFullScreenElement) {
        return document.mozFullScreenElement;
    }
    else if (document.msFullscreenElement) {
        return document.msFullscreenElement;
    }
    else if (document.fullscreenElement) {
        return document.fullscreenElement;
    }
}