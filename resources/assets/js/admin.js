/**
 * 管理側用JavaScript
 */

window.base = {};

//ライブラリ読込
require('./bootstrap');
require('datatables.net-bs');
require('bootstrap-datepicker');
require('bootstrap-datepicker/dist/locales/bootstrap-datepicker.ja.min.js');
require('select2');

//ソース読込
require('./source/util.js');
require('./source/modal.js');
require('./source/datatables.js');
require('./source/form.js');
require('./source/bind.js');
