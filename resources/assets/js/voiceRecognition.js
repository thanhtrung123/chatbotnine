window.base = {};
/**
 * 利用者側用JavaScript
 */
try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}
window.voiceRecognition = require('./speech/voiceRecognition.js');
require('./speech/voiceRecognition-custom.js');
