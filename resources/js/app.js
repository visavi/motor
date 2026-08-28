/* Стили */
import '@tabler/core/dist/css/tabler.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import 'notyf/notyf.min.css';
import '../css/vendor/prettify.css';
import '../css/variables.css';
import '../css/docs.css';
import '../css/app.css';

/* Скрипты. Порядок важен: clipboard ждёт глобальный bootstrap */
import './bootstrap-global.js';
import './theme-toggler.js';
import './vendor/clipboard.js';
import './main.js';
