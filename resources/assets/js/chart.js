/**
 * 利用者側用JavaScript
 */

window.base = {};
require('./dashboard/FileSaver.min.js');
require('./dashboard/Chart.min.js');
require('./dashboard/html2canvas.min.js');
require('./dashboard/taginput.js');
jQuery(function () {
    require('./dashboard/dashboard.js');
});
