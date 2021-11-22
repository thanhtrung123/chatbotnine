/**
 * Disable Screen Scenario
 */
function hiddenScreen() {
    if ($('#wrapper').hasClass('read_only') == false) {
        $('#wrapper').addClass('read_only');
    }
    $('#loadingScenarioModal').removeClass('none');
    $('#loadingScenarioModal').css('display', 'block');
    $('#loadingScenarioModal').css('overflow', 'unset');
}

/**
 * Show Screen Scenario
 */
function showScreen() {
    if ($('#wrapper').hasClass('read_only') == true) {
        $('#wrapper').removeClass('read_only');
    }
    $('#loadingScenarioModal').addClass('none');
    $('#loadingScenarioModal').css('display', 'none');
}

/**
 * Disable button
 */
function disabledButton() {
    // Disable button download zip
    $("#download-zip").prop('disabled', true);
    // Disable button export excel
    $("#export-excel").prop('disabled', true);
    // Disable button import zip
    $("#import-zip").prop('disabled', true);
}

/**
 * Remove disable button
 */
function showButton() {
    // Remove disable button download zip
    $("#download-zip").prop('disabled', false);
    // Remove disable button export excel
    $("#export-excel").prop('disabled', false);
    // Remove disable button import zip
    $("#import-zip").prop('disabled', false);
}

//DOM ロード後
(function($) {
    /**
     * Close modal scenario iexport
     */
    $('.closeModalExport').click(function() {
        $('form#formScenarioIExport').attr("action", '');
        $("#zip").val('');
        $("#uploadName").val('');
        $('.export-zip').val('');
        $('.error_message').css('display', 'none');
        $('.error_message').html('');
        $('#scenarioImportExport').modal('hide'); 
    });

    /**
     * Click button download file zip
     */
    $(document).on('click', '#download-zip', function(e) {
        e.preventDefault();
        disabledButton();
        $("#scenario_ary_group").remove();
        $("#scenario_ary_cate").remove();
        $('form#formScenarioIExport').attr("action", iexport.download_zip);
        $('.export-zip').val('');
        try {
            // Execute Ajax save file zip
            $.ajax({
                type: 'POST',
                url: iexport.ajax_save_zip,
                headers: {
                    'X-CSRF-TOKEN': csrf_token,
                },
                processData: false,
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + Laravel.apiToken);
                },
            }).done(function (response) {
                showButton();
                if (response.status) {
                    $('.export-zip').val(response.file);
                    csrf_token = response._token;
                    $('form#formScenarioIExport').submit();
                } else {
                    alert(response.message);
                }
            }).fail(function (err) {
                showButton();
            });
        } catch (e) {
            showButton();
        }
    });

    /**
     * Click button export file excel
     */
    $(document).on('click', '#export-excel', function(e) {
        e.preventDefault();
        disabledButton();
        $("#scenario_ary_group").remove();
        $("#scenario_ary_cate").remove();
        $('form#formScenarioIExport').attr("action", iexport.download_excel);
        try {
            // Execute Ajax save file excel
            $.ajax({
                type: 'POST',
                url: iexport.ajax_save_excel,
                headers: {
                    'X-CSRF-TOKEN': csrf_token,
                },
                processData: false,
                processData: false,
                contentType: false,
                timeout: 0,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + Laravel.apiToken);
                },
            }).done(function (response) {
                    showButton();
                    $('#formScenarioIExport').append($('<input/>', {
                        type: 'hidden', name: 'scenario_ary_group', id:'scenario_ary_group',
                        value: JSON.stringify(response.scenario_ary_group),
                    }));
                    $('#formScenarioIExport').append($('<input/>', {
                        type: 'hidden', name: 'scenario_ary_cate', id:'scenario_ary_cate',
                        value: JSON.stringify(response.scenario_ary_cate),
                    }));
                    csrf_token = response._token;
                    $('form#formScenarioIExport').submit();
            }).fail(function (err) {
                showButton();
                alert('エラー');
            });
        } catch (e) {
            showButton();
            alert('エラー');
        }
    });

    /**
     * Change file upload zip get name and check size
     */
    $('input[name="zip"]').on('change', function () {
        var filename = $(this).val().split('\\').pop();
        if ($(this).val() != '') {
            $('#uploadName').val(filename);
        }
        if (typeof this.files[0] !== 'undefined') {
            if (this.files[0].size > (parseInt(iexport.post_max_size) * 1024)) {
                $('.error_message').css('display', 'block');
                $('.error_message').html('ファイルのサイズが超えました。' + (parseInt(iexport.post_max_size) * 1024) / 1048576 + 'MB 以下に設定してください');
                $('input[name="zip"]').val('');
                return false;
            }
        }
    })
    
    /**
     * Click button import file zip
     */
    $(document).on('click', '#import-zip', function(e) {
        e.preventDefault();
        $("#store").remove();
        $("#scenario_ary_group").remove();
        $("#scenario_ary_cate").remove();
        var form = $('#formScenarioIExport')[0];
        var formData = new FormData(form);
        var url_ex = iexport.ajax_import_zip;
        disabledButton();
        // Execute Ajax save file zip into server
        try {
            $.ajax({
                type: 'POST',
                url: url_ex,
                headers: {
                    'X-CSRF-TOKEN': csrf_token,
                },
                data: formData,
                enctype: 'multipart/form-data',
                processData: false,
                processData: false,
                contentType: false,
                timeout: 0,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + Laravel.apiToken);
                },
            }).done(function (response) {
                showButton();
                if (response.status == 'error') {
                    $('.error_message').css('display', 'block');
                    $('.error_message').html(response.message);
                } else {
                    $('.error_message').css('display', 'none');
                    $('.error_message').html('');
                    csrf_token = response._token;
                    $('#scenarioExportConfirm').modal('show'); 
                }
            }).fail(function (err) {
                showButton();
                // エラーの場合処理
                console.log(err.status + ' : ' + err.responseText);
            });
        } catch (e) {
            showButton();
            console.log(e.name + ": " + e.message);
        }
    });

    /**
     * Click button confirm import zip
     */
    $(document).on('click', '#import-confirm', function(e) {
        e.preventDefault();
        $('#formScenarioIExport').append($('<input/>', {
            type: 'hidden', name: 'store', id:'store'
        }));
        var form = $('#formScenarioIExport')[0];
        var formData = new FormData(form);
        $('#scenarioExportConfirm').modal('hide'); 
        try {
            // Execute Ajax add data
            $.ajax({
                type: 'POST',
                url: iexport.ajax_import_zip,
                headers: {
                    'X-CSRF-TOKEN': csrf_token,
                },
                data: formData,
                enctype: 'multipart/form-data',
                processData: false,
                processData: false,
                contentType: false,
                timeout: 0,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + Laravel.apiToken);
                    hiddenScreen();
                },
            }).done(function (response) {
                if (response.status == 'error') {
                    showScreen();
                    $("#store").remove();
                    $('.error_message').css('display', 'block');
                    $('.error_message').html(response.message);
                } else {
                    location.reload();
                }
            }).fail(function (err) {
                showScreen();
                $("#store").remove();
            });
        } catch (e) {
            showScreen();
            $("#store").remove();
        }
    });
})(jQuery);