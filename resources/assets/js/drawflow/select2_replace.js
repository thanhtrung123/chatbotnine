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
 * 汎用DOMコピーボタン
 */
$(document).on('click', '[data-dom-add-select2]', function (e) {
    let data = $(this).data('domAddSelect2'), tpl_html = base.util.findDom(data.src).clone(true).html();
    if (data.idx_src != void 0) {
        let idx_src = base.util.findDom(data.idx_src), next_idx = 0;
        idx_src.each(function () {
            if (next_idx < $(this).val() - 0) next_idx = $(this).val() - 0;
        });
        tpl_html = base.util.replaceTemplateValue(tpl_html, { idx: next_idx + 1 });
    }
    let src = $('<div/>').html(tpl_html).children(), area = base.util.findDom(data.area);
    area.append(src);
    var div_dropdown_select2 = $('#dropdown_select2');
    if (typeof getFullscreenElement() !== 'undefined') {
        div_dropdown_select2 = $('#mySelect2');
    }
    $(src).find('select').select2({
        dropdownParent: $(div_dropdown_select2),
        createTag: function (params) {
            var term = $.trim(params.term);
            var options_selected = $(src).find('select').val();
            if (term === '' || options_selected.indexOf(term) > -1) {
                $(src).find('input.select2-search__field').val('');
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // add additional parameters
            }
        },
        tags: true,
        allowClear: false,
        selectOnClose: false,
        debug: true,
        tokenSeparators: [' '],
        matcher: function matchCustom(params, data) {
            if (oldParams != params.term) {
                oldParams = params.term;
                modifiedData = {};
                dataSame = {};
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
    })
})

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
 * Delete item in select2
 * @param idx
 */
function delAllItemSelect (idx) {
    $('#select' + idx).val(null).trigger('change');
    $('#select' + idx).select2('close');
}

/**
 * Replace input to select2
 * @param input
 */
function replaceInputToSelect2(input) {
    var elem = $(input).closest(".keyword_block");
    var elem_select = elem.find("select");
    var new_val_input = $(input).val();
    var old_val_select = elem_select.val();
    var same_old_val = old_val_select.indexOf(new_val_input);
    if ($(input).val() == '') {
        old_val_select.splice(index_remove, 1);
    } else {
        old_val_select.splice(index_remove, 1);
        old_val_select.splice(index_remove, 0, new_val_input);
    }
    createOptionSelectKeyWordAll(elem_select, old_val_select, all_keywords);
    $(input).closest('ul').append('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" aria-controls="select2-select2-results"></li>')
    setTimeout(function () {
        $(elem_select).find('.select2 .select2-selection__rendered li:last-child input').focus();
        addBtnClearAll(elem_select);
    })
}

/**
 * Replace select2 to input
 * @param option_select
 */
function replaceSelect2ToInput(option_select) {
    var elem = $(option_select).closest(".keyword_block");
    var elem_select = elem.find("select");
    var old_val_select = elem_select.val();
    var old_val = $(option_select).text().substring(1);
    index_remove = old_val_select.indexOf(old_val);
    var elem_input = $('<li class="select2-selection__choice_replace"><input type="text" class="input_replace-select" value="' + old_val + '"/></li>');
    $(option_select).closest('ul').find('.select2-search__field').parent().remove();
    elem_input.insertAfter($(option_select));
    $(option_select).remove();
    elem_input.find('input').focus();
}

/**
 * Create option keyword select2
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
 * Event unselect of select2 not display dropdown select2
 */
$(document).on("select2:unselect", ".select2-keyword", function (evt) {
    if (!evt.params.originalEvent) {
        return;
    }
    evt.params.originalEvent.stopPropagation();
    addBtnClearAll($(evt.currentTarget));
});

/**
 * Event select of select2
 */
$(document).on("select2:select", ".select2-keyword", function (evt) {
    addBtnClearAll($(evt.currentTarget));
});

/**
 * Event unselecting of select2 backspace
 */
$(document).on("select2:unselecting", ".select2-keyword", function (e) {
    var event = window.event;
    var is_edit =  $(e.currentTarget).next().find('input.input_replace-select').length != 0;
    if (typeof event !== 'undefined' && event.keyCode === 8) {
       if (is_edit) {
        return false;
       }
    }
    setTimeout(() => {
        addBtnClearAll($(e.currentTarget));
    });
});

/**
 * Event selecting of select2
 */
$(document).on("select2:selecting", ".select2-keyword", function (evt) {
    var elem_select = $(evt.currentTarget);
    var elem_select_val = elem_select.val();
    elem_select_val.push(evt.params.args.data.id);
    createOptionSelectKeyWordAll(evt.currentTarget, elem_select_val, all_keywords);
});

/**
 * Event opening of select2
 */
$(document).on("select2:opening", ".select2-keyword", function (evt) {
    var elem_select = $(evt.currentTarget);
    if($(elem_select).next().find('input.input_replace-select').length > 0) {
        return false;
    };
});

/**
 * Event open of select2
 */
$(document).on("select2:open", ".select2-keyword", function (evt) {
    setTimeout(() => {
        var elem_option_hightlighted = $('#mySelect2').find('li.select2-results__option--highlighted');
        if($(elem_option_hightlighted).length > 0) {
            elem_option_hightlighted.removeClass('select2-results__option--highlighted');
        };
        $('#mySelect2').find('li').first().addClass('select2-results__option--highlighted')
    }, 10);
});

/**
 * Event keydown (enter:13, space:32, tab:9 then blur input)
 */
$(document).on("keydown", ".input_replace-select", function (event) {
    var charCode = event.which || event.keyCode;
    if (charCode === 13 || charCode === 9 || charCode === 32) {
        var elem = $(this).closest(".keyword_block");
        var elem_select = elem.find("select");
        var new_val_input = $(this).val();
        var old_val_select = elem_select.val();
        var same_old_val = old_val_select.indexOf(new_val_input);
        if (same_old_val > -1 && same_old_val != index_remove) {
            $(this).val('');
            return false;
        }
        $('.input_replace-select').blur();
    }
});

/**
 * Double click change edit input select2
 */
$(document).on("dblclick", ".select2-selection__choice", function (event) {
    var elem = $(this).closest(".keyword_block");
    var elem_select = elem.find("select");
    $(elem_select).select2("close");
    replaceSelect2ToInput(this);
});

/**
 * Blur input
 */
$(document).on("blur", ".input_replace-select", function (event) {
    replaceInputToSelect2(this);
});

/**
 * Click button clear all item in select2
 */
$(document).on('click', ".select2-selection__clear", function() {
    var elem = $(this).closest(".keyword_block");
    var elem_select = elem.find("select");
    var id = elem_select.attr('id').substring(6);
    delAllItemSelect(id)
});

/**
 * Add button clear all into input select2
 */
function addBtnClearAll(elem) {
    var allowClear = $('<span class="select2-selection__clear">×</span>');
    if ($(elem).next().find('span.select2-selection__clear').length == 0 && $(elem).val().length) {
        $(elem).next().find('.select2-selection__rendered').prepend(allowClear);
    }
}

/**
 * Get full screen element
 */
let getFullscreenElement = () => {
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
};