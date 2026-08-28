import * as tabler from '@tabler/core/dist/js/tabler.min.js';

// Разметка обращается к bootstrap напрямую (модалка стикеров, подсказки),
// поэтому кладём его в глобальную область, как это делал bootstrap.bundle
window.bootstrap = tabler.bootstrap ?? tabler;
