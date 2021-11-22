/**
 * 音声認識用クラス
 */
module.exports = class voiceRecognition {
    /**
     * コンストラクタ
     */
    constructor(options) {
        // 設定値
        this.options = {
            // 音声関係の設定値
            audio: {
                buffer_size: ((!options.AUDIO.BUFFER_SIZE) ? 4096 : options.AUDIO.BUFFER_SIZE),
                lang: ((!options.AUDIO.LANG) ? 'ja-JP' : options.AUDIO.LANG),
            },
            // 描画関係の設定値
            wave_view: {
                flag: ((!options.WAVE_VIEW.FLAG) ? false : options.WAVE_VIEW.FLAG),
                fill_style: ((!options.WAVE_VIEW.FILL_STYLE) ? 'rgb(16, 16, 24)' : options.WAVE_VIEW.FILL_STYLE),
                stroke_style: ((!options.WAVE_VIEW.STROKE_STYLE) ? 'rgb(124, 224, 255)' : options.WAVE_VIEW.STROKE_STYLE),
            }
        }
        this.audio_context;
        this.input_source;
        this.processor;
        this.recognition = null;
        this.rec_stream;
        this.audio_data = [];
        this.canvas;
        this.canvas_ctx;
        this.analyzer = null;
        this.render_anim;
        this.out_time = 0;
        // 3s time out
        this.max_time_silent = ((!options.TIME_OUT) ? 3000 : options.TIME_OUT);
        this.requestAnimationFrame = true;
        try {
            // マルチブラウザ対策
            navigator.mediaDevices.getUserMedia = navigator.mediaDevices.getUserMedia ||
                navigator.mediaDevices.webkitGetUserMedia ||
                navigator.mozmediaDevices.getUserMedia ||
                navigator.msmediaDevices.getUserMedia ||
                navigator.omediaDevices.getUserMedia;

            window.AudioContext = window.AudioContext ||
                window.webkitAudioContext ||
                window.mozAudioContext ||
                window.msAudioContext ||
                window.oAudioContext;

            window.URL = window.URL ||
                window.webkitURL ||
                window.mozURL ||
                window.msURL ||
                window.oURL;

            window.SpeechRecognition = window.SpeechRecognition ||
                window.webkitSpeechRecognition ||
                window.mozSpeechRecognition ||
                window.msSpeechRecognition ||
                window.oSpeechRecognition;

            window.requestAnimationFrame = window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                window.oRequestAnimationFrame;

            window.cancelAnimationFrame = window.cancelAnimationFrame ||
                window.webkitcancelAnimationFrame ||
                window.mozcancelAnimationFrame ||
                window.mscancelAnimationFrame ||
                window.ocancelAnimationFrame;

            // 音声取得用ストリームの利用可否を確認する
            this.media_promise = navigator.mediaDevices.getUserMedia({ audio: true });
        } catch (e) {
            console.log(e.name + ": " + e.message);
            return false;
        }
        return this;
    }

    init(stream) {
        try {
            // ストリーム情報の保持
            this.rec_stream = stream;
            // 音声取得用ストリームへ接続する
            this.audio_context = new window.AudioContext();
            this.processor = this.audio_context.createScriptProcessor(this.options.audio.buffer_size, 1, 1);
            // 文字入力をリアルタイムで表示する
            if (SpeechRecognition !== undefined) {
                this.recognition = new SpeechRecognition();
                this.recognition.interimResults = true;
                this.recognition.continuous = true;
            }
            // 音声入力用ストリーミングに接続
            this.input_source = this.audio_context.createMediaStreamSource(this.rec_stream);
        } catch (e) {
            console.log(e.name + ": " + e.message);
            return false;
        }
        return true;
    }

    setProcessorOnaudioprocess(func) {
        this.processor.onaudioprocess = func;
    }

    setProcessorStop(func) {
        this.processor.stop = func;
    }

    setWaveView(canvas, fill_style, line_style) {
        // プロパティへ設定
        this.canvas = canvas;
        this.options.wave_view.flag = true;
        this.options.wave_view.fill_style = ((!fill_style) ? this.options.wave_view.fill_style : fill_style);
        this.options.wave_view.stroke_style = ((!line_style) ? this.options.wave_view.stroke_style : line_style);

        // 解析用
        this.analyzer = this.audio_context.createAnalyser();
        // 表示用(初期表示)
        this.canvas_ctx = this.canvas.getContext('2d');
        this.canvas_ctx.fillStyle = this.options.wave_view.fill_style;
        this.canvas_ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
    }

    start(lang) {
        // 言語を指定
        this.options.audio.lang = ((!lang) ? this.options.audio.lang : lang);
        // 入力ソースとコネクト
        this.input_source.connect(this.processor);
        this.processor.connect(this.audio_context.destination);
        // 文字表示用
        if (this.recognition !== null) {
            this.recognition.lang = this.options.audio.lang;
            this.recognition.stop();
            this.recognition.start();
        }
        if (this.options.wave_view.flag) {
            this.input_source.connect(this.analyzer);
            var times = new Uint8Array(this.analyzer.frequencyBinCount);
            this.out_time = null;
            this.waveDraw(times, this.analyzer, this.canvas, this.canvas_ctx, this.options, null);
        }
    }

    stop() {
        clearTimeout(timeout_id);
        $('#rec_canvas').hide();
        $('#voice_btn').removeClass('rec');
        this.processor.disconnect();
        if (!is_safari) {
            this.input_source.disconnect();
        }
        this.processor.stop();
        if (this.recognition !== null) {
            this.recognition.stop();
        }
        if (this.options.wave_view.flag) {
            window.cancelAnimationFrame(this.render_anim);
        }
    }

    waveDraw(times, analyzer, canvas, canvas_ctx, options, timestamp) {
        this.requestAnimationFrame = true;
        // 0~1まで設定でき、0に近いほど描画の更新がスムーズになり, 1に近いほど描画の更新が鈍くなる。
        analyzer.smoothingTimeConstant = 0.5;
        // FFTサイズを指定する。デフォルトは2048。
        analyzer.fftSize = options.audio.buffer_size;
        // 時間領域の波形データを引数の配列に格納するメソッド。
        // analyzer.fftSize / 2の要素がthis.timesに格納される。今回の配列の要素数は1024。
        analyzer.getByteTimeDomainData(times);
        // 全ての波形データを描画するために、一つの波形データのwidthを算出する。
        var barWidth = canvas.width / analyzer.frequencyBinCount;
        canvas_ctx.fillStyle = options.wave_view.fill_style;
        canvas_ctx.fillRect(0, 0, canvas.width, canvas.height);
        // 無音時間が音声認識設定ファイル(Speech.timeout)の設定時間に達したら音声入力終了
        if (timestamp && !this.out_time) {
            this.out_time = timestamp;
        }
        // ノイズ発生時の値判定範囲を「127～129」として無音と判断する
        if (times[analyzer.frequencyBinCount - 1] >= 108 && times[analyzer.frequencyBinCount - 1] <= 148) {
            if (timestamp && (timestamp - this.out_time > this.max_time_silent)) {
                this.requestAnimationFrame = false;
                this.stop();
            }
        } else {
            this.requestAnimationFrame = true;
            if(timestamp) this.out_time = timestamp
        }
        // analyzer.frequencyBinCountはanalyzer.fftSize / 2の数値。よって今回は1024。
        for (var i = 0; i < analyzer.frequencyBinCount; ++i) {
            var value = times[i]; // 波形データ 0 ~ 255までの数値が格納されている。
            var percent = value / 255; // 255が最大値なので波形データの%が算出できる。
            var height = canvas.height * percent; // %に基づく高さを算出
            var offset = canvas.height - height;  // y座標の描画開始位置を算出
            canvas_ctx.fillStyle = options.wave_view.stroke_style;
            canvas_ctx.fillRect(i * barWidth, offset, barWidth, 2);
        }
        if (this.requestAnimationFrame) {
            this.render_anim = window.requestAnimationFrame(this.waveDraw.bind(this, times, analyzer, canvas, canvas_ctx, options));
        }
    }

    // WavBlobデータへエンコードする
    encodeWavBlob(audio_data, sample_rate) {
        var encodeWAV = function (samples, sampleRate) {
            var buffer = new ArrayBuffer(44 + samples.length * 2);
            var view = new DataView(buffer);
            var writeString = function (view, offset, string) {
                for (var i = 0; i < string.length; i++) {
                    view.setUint8(offset + i, string.charCodeAt(i));
                }
            };
            var floatTo16BitPCM = function (output, offset, input) {
                for (var i = 0; i < input.length; i++ , offset += 2) {
                    var s = Math.max(-1, Math.min(1, input[i]));
                    output.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
                }
            };
            writeString(view, 0, 'RIFF');  // RIFFヘッダ
            view.setUint32(4, 32 + samples.length * 2, true); // これ以降のファイルサイズ
            writeString(view, 8, 'WAVE'); // WAVEヘッダ
            writeString(view, 12, 'fmt '); // fmtチャンク
            view.setUint32(16, 16, true); // fmtチャンクのバイト数
            view.setUint16(20, 1, true); // フォーマットID
            view.setUint16(22, 1, true); // チャンネル数
            view.setUint32(24, sampleRate, true); // サンプリングレート
            view.setUint32(28, sampleRate * 2, true); // データ速度
            view.setUint16(32, 2, true); // ブロックサイズ
            view.setUint16(34, 16, true); // サンプルあたりのビット数
            writeString(view, 36, 'data'); // dataチャンク
            view.setUint32(40, samples.length * 2, true); // 波形データのバイト数
            floatTo16BitPCM(view, 44, samples); // 波形データ
            return view;
        };

        var mergeBuffers = function (audioData) {
            var sampleLength = 0;
            for (var i = 0; i < audioData.length; i++) {
                sampleLength += audioData[i].length;
            }
            var samples = new Float32Array(sampleLength);
            var sampleIdx = 0;
            for (var i = 0; i < audioData.length; i++) {
                for (var j = 0; j < audioData[i].length; j++) {
                    samples[sampleIdx] = audioData[i][j];
                    sampleIdx++;
                }
            }
            return samples;
        };

        // Wavデータへエンコードする
        var dataview = encodeWAV(mergeBuffers(audio_data), sample_rate);
        // WavデータをBlob型にする
        var audio_blob = new Blob([dataview], { type: 'audio/wav' });
        return audio_blob;
    }
}