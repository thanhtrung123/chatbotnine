/**
 * 利用者側用JavaScript
 */

window.base = {};
window.jQuery = window.$ = require('jquery');
require('./source/util.js');
base.user = {
    conf: {
        close_event: {
            beforeunload: 'beforeunload',
            pagehide: 'pagehide',
            unload: 'unload',
        }
    },
};

/**
 * ユーザーログ
 * @param chat_id
 * @param uri
 * @param status
 * @param async
 */
base.user.userLog = (chat_id, uri, status, close = false, channel = 1) => {
    let params = {
        id: chat_id,
        status: status,
        channel: channel,
    };
    if (close && window.navigator && window.FormData) {
        const data = new FormData();
        $.each(params, (k, v) => {
            data.append(k, v)
        });
        navigator.sendBeacon(uri, data);
    } else {
        base.util.ajax('POST', uri, params, 'json', {
            async: !close,
        });
    }
};

/**
 *
 */
base.user.getCloseEvent = () => {
    let ua = window.navigator.userAgent,
        ret = base.user.conf.close_event.beforeunload;
    //必須UAチェック
    if (ua.match(new RegExp(CLOSE_UA.require_regexp[0], CLOSE_UA.require_regexp[1]))) {
        //ターゲット取得
        $.each(CLOSE_UA.target_regexp, (k, v) => {
            if (ua.match(new RegExp(v.match[0], v.match[1]))) {
                let ver = ua.match(new RegExp(v.version[0], v.version[1]));
                if (ver) {
                    let vtmp = ver[v.version[2]].replace(/_/g, '.').match(/^\d+\.\d+/);
                    ver = vtmp - 0.0;
                    if (ver > v.version[3]) {
                        ret = base.user.conf.close_event.pagehide;
                    } else {
                        ret = base.user.conf.close_event.unload;
                    }
                    return false;
                }

            }
        });
    }
    return ret;
};