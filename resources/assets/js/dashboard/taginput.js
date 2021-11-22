// MIT License

// Copyright (c) 2020 cris_0xC0

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.



function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }
var input_create = $('<input />', {
    'type': 'text',
    'name': 'add-tags',
    'class': 'add-tags create-tags',
    'autocomplete': 'off',
    'id' : 'add-tags',
    'oninput' : "this.value = this.value.replace(/[^0-9.    ]*/g, '');"
});
(function ($) {
    var methods = {
        init: function init(options) {
            // add our input tag
            $(this).html("<span class=\"tags-wrapper\"></span><input text=\"\" oninput=\"this.value = this.value.replace(/[^0-9. \t]*/g, '');\" class=\"add-tags create-tags\" name=\"add-tags\" id=\"add-tags\" placeholder=\"".concat(options.tagInputPlaceholder, "\" value=\"\" autocomplete=\"off\"/>")); // initialize tags

            $.each(options.initialTags, function (_, value) {
                addTag(value, options.tagBackgroundColor, options.tagColor, options.tagBorderColor, options.tagHiddenInput);
            }); // focus on our input on the container clicked

            if ($(".tags-wrapper").find(".edit-tags").length == 0) {
                $(this).parent().click(function (event) {
                    $(".create-tags").focus();
                }); // add tag on key down
            }
            $(document).on("keydown focusout", ".add-tags", function (evt) {
                if (evt.keyCode == 32 | evt.keyCode == 9 | evt.keyCode == 13 | evt.type == 'focusout') {
                    evt.preventDefault();
                    var tag = $.trim($(this).val());
                    if (tag.length < 1) {
                        if ($('.edit-tags')) {
                            $('.edit-tags').remove();
                            if ($(".myContainer").find(".create-tags").length == 0) {
                                $(".myContainer").append(input_create);
                            }
                        }
                        return false;
                    }
                    if (evt.keyCode == 32 | evt.keyCode == 9 | evt.keyCode == 13) {
                        $(this).unbind("focusout");
                    }
                    addTag(tag, options.tagBackgroundColor, options.tagColor, options.tagBorderColor, options.tagHiddenInput);
                    $(this).val("");
                    $(this).focus();
                }
            }); // remove tag on close icon click
            $(document).on("click", ".tag-remove", function () {
                var tag = $(this).attr("tag");
                $("[tag-title='".concat(tag, "']")).remove();
                copyTags(options.tagHiddenInput);
                $(".tags-wrapper").focus();
            });

            $(document).on("dblclick", ".tags", function () {
                if ($(".myContainer").find('.create-tags').length != 0) {
                    $(".myContainer").find('.create-tags').remove();
                }
                var str = this.outerHTML;
                var tagName = this.getAttribute('data-tagname');
                var input = $('<input />', {
                    'type': 'text',
                    'name': 'add-tags',
                    'class': 'add-tags edit-tags',
                    'autocomplete': 'off',
                    'value': tagName,
                    'oninput' : "this.value = this.value.replace(/[^0-9. \t]*/g, '');"
                });
                $(this).replaceWith(input);
                // $(this).parent().append(input);
                // $(this).remove();
                input.focus();
            });
            return $(this).css({
                "border-color": options.tagContainerBorderColor,
                "border-width": ".1em",
                "border-style": "solid"
            });
        }
    }; // add tag

    function addTag(tagName, tagBackgroundColor, tagColor, tagBorderColor, tagHiddenInput) {
        if (!$("[tag-title='".concat(tagName, "']")).length) {
            var tagHTML = "<span style=\"cursor: pointer;background-color: ".concat(tagBackgroundColor, "; color: ").concat(tagColor, "; border-color: ").concat(tagBorderColor, "\" class=\"tags\" data-tagName='"+tagName+"')\" tag-title=\"").concat(tagName, "\">\n        ").concat(tagName, "\n        <a title=\"Remove tag\" class=\"tag-remove\" tag=\"").concat(tagName, "\">\n            <i class=\"fa fa-times\"></i>\n        </a>\n        </span>");
            if ($(".tags-wrapper").find(".edit-tags").length != 0) {
                let elem = $(".tags-wrapper").find(".edit-tags");
                $(tagHTML).insertAfter(".edit-tags");
                // $(elem).replaceWith(tagHTML);
                $(".edit-tags").remove();
                $(this).unbind("focusout");
            } else {
                $(".tags-wrapper").append(tagHTML);
            }

            if ($(".myContainer").find(".create-tags").length == 0) {
                $(".myContainer").append(input_create);
            }
            // if ($(".tags-wrapper").find(".edit-tags").length != 0) {
            //     $(".tags-wrapper").find(".edit-tags").remove();
            // }
            copyTags(tagHiddenInput);
        }
    } // add tag to the hidden input


    function copyTags(tagHiddenInput) {
        var listOfTags = [];
        $(".tags").each(function () {
            listOfTags.push($(this).text().trim());
        });
        tagHiddenInput.val(listOfTags.join(","));
    } // declare our tag input plugin



    $.fn.TagsInput = function (methodOrOptions) {
        if (_typeof(methodOrOptions) === 'object' || !methodOrOptions) {
            return methods.init.apply(this, arguments);
        } else if (methods[methodOrOptions]) {
            return methods[methodOrOptions].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            $.error('Method ' + methodOrOptions + ' does not exist on jQuery.TagsInput');
        }
    };
})(jQuery);