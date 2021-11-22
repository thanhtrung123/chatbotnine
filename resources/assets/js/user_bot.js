/**
 * ChatBot用JavaScript
 * ※user.jsを先に読み込まないと使用できません。
 */
require('./bot/bot.js');
require('./source/suggest.js');
jQuery(function () {
    require('./bot/valid.js');
});
