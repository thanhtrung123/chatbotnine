jQuery(function ($) {
    // 音声取得用ストリームへ接続する
    voiceRecog = new voiceRecognition(OPTIONS);
    voiceRecog.media_promise.then(function (stream) {
        // 初期化
        voiceRecog.init(stream);
        setTimeout(() => {
            if (!stream.active) {
                return;
            }
            if ($('#voice_btn_disabled').find('.fa').hasClass('fa-microphone-slash')) {
                $('#voice_btn_disabled').find('.fa').removeClass('fa-microphone-slash').addClass('fa-microphone');
                $("#voice_btn_disabled").attr('id', 'voice_btn');
            }
            // 入力内容をリアルタイムに表示
            if (voiceRecog.recognition !== null) {
                voiceRecog.recognition.onresult = function (e) {
                    // 音声情報アニメーション表示処理
                    for (let i = e.resultIndex; i < e.results.length; i++) {
                        $('#txt_input').val(e.results[i][0].transcript);
                    }
                };
            }
            // 波形データの表示
            voiceRecog.setWaveView($('#canvas').get(0));
            // データの取得（audio_data配列に保存）
            voiceRecog.setProcessorOnaudioprocess(function (e) {
                // オーディオ入力
                var data = e.inputBuffer.getChannelData(0);
                var buffer_data = new Float32Array(voiceRecog.options.audio.buffer_size);
                for (var i = 0; i < voiceRecog.options.audio.buffer_size; i++) {
                    buffer_data[i] = data[i];
                }
                voiceRecog.audio_data.push(buffer_data);
            });
            // 録音停止イベント
            voiceRecog.setProcessorStop(function () {
                var blob = voiceRecog.encodeWavBlob(voiceRecog.audio_data, voiceRecog.audio_context.sampleRate);
                voiceRecog.audio_data = [];
                var formdata = new FormData();
                formdata.append('audio-blob', blob);
                if ($('#wrapper').hasClass('read_only') == false) {
                    $('#wrapper').addClass('read_only');
                }
                $('#loadingAudioModal').removeClass('none');
                try {
                    $.ajax({
                        type: 'POST',
                        url: url_upload,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        data: formdata,
                        processData: false,
                        processData: false,
                        contentType: false,
                    }).done(function (data) {
                        if (data.status == true) {
                            $('#txt_input').val(data.message);
                            if (data.message == '') {
                                $('#emptyAudioModal').removeClass('none');
                                $('.closeModalAudio').focus();
                                $('.closeModal').on('click', function () {
                                    $('#emptyAudioModal').addClass('none');
                                    $('#wrapper').removeClass('read_only');
                                });
                            } else {
                                $('#wrapper').removeClass('read_only');
                                $('#txt_input').get(0).setCustomValidity('');
                                $('#button_submit').trigger('click');
                            }
                        } else {
                            $('#emptyAudioModal').find('.modal-content p').text(data.message)
                            $('#emptyAudioModal').removeClass('none');
                            $('.closeModalAudio').focus();
                            $('.closeModal').on('click', function () {
                                $('#emptyAudioModal').addClass('none');
                                $('#wrapper').removeClass('read_only');
                            });
                        }
                    }).fail(function (err) {
                        // エラーの場合処理
                        console.log(err.status + ' : ' + err.responseText);
                    }).always(function () {
                        $('#loadingAudioModal').addClass('none');
                    });
                } catch (e) {
                    console.log(e.name + ": " + e.message);
                }
            });

            // 音声ボタンクリック時
            $("#voice_btn").on('click', function () {
                // 入力開始
                if ($('#voice_btn').hasClass('rec')) {
                    return;
                }
                try {
                    $('#wrapper').addClass('read_only');
                    voiceRecog.start();
                    timeout_id = setTimeout(function (e) {
                        voiceRecog.beforeStop();
                        voiceRecog.stop();
                    }, max_time_out);
                    $('#voice_btn').addClass('rec');
                    $('#rec_canvas').show();
                } catch (e) {
                    console.log(e);
                }
            });
        }, 10)
    }).catch(function (err) {
        if ($('#voice_btn').find('.fa').hasClass('fa-microphone')) {
            $('#voice_btn').find('.fa').removeClass('fa-microphone').addClass('fa-microphone-slash');
            $("#voice_btn").attr('id', 'voice_btn_disabled');
        }
        console.log(err.name + ": " + err.message);
    });

    // 停止前処理の追加
    voiceRecog.beforeStop = function () {
        clearTimeout(timeout_id);
        $('#rec_canvas').hide();
        $('#voice_btn').removeClass('rec');
        $('#wrapper').removeClass('read_only');
    }
});