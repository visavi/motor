import { Fancybox } from '@fancyapps/ui';
import { Notyf } from 'notyf';
import Tags from 'bootstrap5-tags';
import './vendor/prettify.js';

const notyf = new Notyf({
    duration: 4000,
    ripple: false,
    position: { x: 'center', y: 'top' },
});

const message = {
    success: (text) => text && notyf.success(text),
    error: (text) => text && notyf.error(text),
};

/**
 * Отправляет форму и разбирает json-ответ движка
 */
async function post(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: data,
    });

    return response.json();
}

function formData(values) {
    const data = new FormData();

    for (const [key, value] of Object.entries(values)) {
        if (value !== undefined && value !== null) {
            data.append(key, value);
        }
    }

    return data;
}

/* ── Счётчик символов ─────────────────────────────────────────────── */

function updateCounter(textarea) {
    const counter = textarea.closest('.mb-3')?.querySelector('.js-textarea-counter')
        ?? document.querySelector('.js-textarea-counter');

    if (! counter) {
        return;
    }

    const maxLength = parseInt(textarea.getAttribute('maxlength'), 10);
    const length = textarea.value.replace(/(\r\n|\n|\r)/g, '\r\n').length;

    counter.classList.toggle('text-danger', length > maxLength);
    counter.textContent = length === 0 ? '' : 'Осталось символов: ' + (maxLength - length);
}

/* ── Инициализация страницы ───────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.prettyPrint === 'function') {
        window.prettyPrint();
    }

    Fancybox.bind('[data-fancybox]');

    document.querySelectorAll('.input-tag').forEach((select) => {
        Tags.init(select, {
            allowNew: true,
            server: '/tag',
            liveServer: true,
            clearEnd: true,
            allowClear: true,
            suggestionsThreshold: 2,
            max: parseInt(select.dataset.max ?? '0', 10) || undefined,
            separator: [','],
            addOnBlur: true,
        });
    });

    document.addEventListener('input', (event) => {
        if (event.target.matches('textarea[maxlength]')) {
            updateCounter(event.target);
        }
    });

    document.addEventListener('click', (event) => {
        const title = event.target.closest('.spoiler-title');

        if (title) {
            const spoiler = title.parentElement;
            const text = spoiler.querySelector('.spoiler-text');

            spoiler.classList.toggle('spoiler-open');

            if (text) {
                text.hidden = ! text.hidden;
            }
        }
    });
});

/* ── Комментарии ──────────────────────────────────────────────────── */

/**
 * Отправляет комментарий без перезагрузки страницы
 */
window.sendComment = function (el) {
    const wrapper = el.closest('.js-form');
    const form = wrapper.querySelector('form');
    const textarea = wrapper.querySelector('textarea[name="text"]');

    post(form.getAttribute('action'), formData({
        text: textarea.value,
        csrf: wrapper.querySelector('input[name="csrf"]').value,
        parent_id: wrapper.querySelector('input[name="parent_id"]')?.value,
    })).then((data) => {
        if (! data.success) {
            textarea.classList.add('is-invalid');

            const feedback = wrapper.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = data.message;
                feedback.style.display = 'block';
            }

            message.error(data.message);

            return;
        }

        const currentPost = wrapper.closest('.post');
        const newPost = document.querySelector('.js-post').cloneNode(true);
        const depth = parseInt(currentPost.dataset.depth ?? '0', 10) + 1;

        newPost.classList.remove('js-post');
        newPost.hidden = false;
        newPost.style.display = '';
        newPost.querySelector('.post-message').innerHTML = data.comment.text;
        newPost.querySelector('.post-date').textContent = data.comment.created_at;
        newPost.id = 'comment_' + data.comment.id;
        newPost.style.marginLeft = depth * 20 + 'px';

        currentPost.after(newPost);
        wrapper.remove();

        message.success(data.message);
    });

    return false;
};

/**
 * Переход к форме ввода
 */
window.postJump = function () {
    document.querySelector('.post-form')?.scrollIntoView({ behavior: 'smooth' });
};

/**
 * Открывает форму ответа под сообщением
 */
function openForm(el, fill) {
    document.querySelectorAll('.js-answer').forEach((form) => form.remove());

    const post = el.closest('.post');
    const form = document.querySelector('.js-form').cloneNode(true);

    form.classList.add('js-answer');
    form.hidden = false;
    form.style.display = '';

    const parent = form.querySelector('[name="parent_id"]');
    if (parent) {
        parent.value = post.id.replace(/^comment_/, '');
    }

    post.append(form);

    const textarea = form.querySelector('textarea');
    textarea.value = fill(post);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = textarea.value.length;

    return false;
}

/**
 * Ответ на сообщение
 */
window.postReply = function (el) {
    return openForm(el, (post) => post.querySelector('.post-author').dataset.login + ', ');
};

/**
 * Цитирование сообщения
 */
window.postQuote = function (el) {
    return openForm(el, (post) => {
        const author = post.querySelector('.post-author').dataset.login;
        const date = post.querySelector('.post-date').textContent;
        const text = post.querySelector('.post-message').cloneNode(true);

        text.querySelectorAll('blockquote').forEach((quote) => quote.remove());

        return '[quote=' + author + ' ' + date + ']' + text.textContent.trim() + '[/quote]\n';
    });
};

/* ── Действия ─────────────────────────────────────────────────────── */

/**
 * Отправляет скрытую форму по ссылке
 */
window.submitForm = function (el) {
    if (! confirm(el.dataset.confirm ?? 'Вы подтверждаете действие?')) {
        return false;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = el.getAttribute('href');

    const fields = { csrf: el.dataset.csrf };

    if (el.dataset.method) {
        fields._METHOD = el.dataset.method.toUpperCase();
    }

    for (const [name, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.append(input);
    }

    document.body.append(form);
    form.submit();

    return false;
};

/**
 * Вставка текста в поле ввода на месте курсора
 */
window.pasteText = function (paste) {
    const field = document.querySelector('.js-answer textarea, .post-form textarea, textarea[name="text"]');

    if (! field) {
        return;
    }

    const start = field.selectionStart ?? field.value.length;
    const end = field.selectionEnd ?? field.value.length;

    field.value = field.value.slice(0, start) + paste + field.value.slice(end);
    field.focus();
    field.selectionStart = field.selectionEnd = start + paste.length;

    updateCounter(field);
};

/**
 * Вставка изображения в форму
 */
window.pasteImage = function (source) {
    const path = typeof source === 'string'
        ? source
        : source.querySelector('img')?.getAttribute('src');

    if (path) {
        window.pasteText('[img]' + path + '[/img]');
    }

    return false;
};

/**
 * Удаление изображения из формы
 */
window.cutImage = function (path) {
    const field = document.querySelector('textarea[name="text"]');

    if (field) {
        field.value = field.value.replace('[img]' + path + '[/img]', '');
        updateCounter(field);
    }
};

/**
 * Загрузка файла
 */
window.submitFile = function (el) {
    const files = document.querySelector('.js-files');
    const spinner = document.createElement('span');
    spinner.className = 'spinner-border mx-3';

    files?.append(spinner);

    post('/upload', formData({
        file: el.files[0],
        id: el.dataset.id,
        type: el.dataset.type,
        csrf: el.dataset.csrf,
    })).then((data) => {
        spinner.remove();

        if (! data.success) {
            message.error(data.message);

            return;
        }

        let template;

        if (data.type === 'image') {
            template = document.querySelector('.js-image-template').cloneNode(true);
            template.querySelector('img').src = data.path;

            window.pasteImage(data.path);
        } else {
            template = document.querySelector('.js-file-template').cloneNode(true);

            const link = template.querySelector('.js-file-link');
            link.href = data.path;
            link.textContent = data.name;

            template.querySelector('.js-file-size').textContent = data.size;
        }

        template.querySelector('.js-file-delete').dataset.id = data.id;
        files?.insertAdjacentHTML('beforeend', template.innerHTML);
    }).catch(() => spinner.remove());

    el.value = '';

    return false;
};

/**
 * Удаление файла
 */
window.deleteFile = function (el) {
    post('/upload/' + el.dataset.id, formData({
        type: el.dataset.type,
        csrf: el.dataset.csrf,
        _METHOD: 'DELETE',
    })).then((data) => {
        if (! data.success) {
            message.error(data.message);

            return;
        }

        window.cutImage(data.path);
        el.closest('.js-file')?.remove();
    });

    return false;
};

/**
 * Изменение рейтинга
 */
window.changeRating = function (el) {
    post('/rating/' + el.dataset.id, formData({
        type: el.dataset.type,
        vote: el.dataset.vote,
        csrf: el.dataset.csrf,
    })).then((data) => {
        if (! data.success) {
            message.error(data.message);

            return;
        }

        const widget = el.closest('.js-rating');

        widget.querySelectorAll('a').forEach((link) => link.classList.remove('active'));

        if (! data.cancel) {
            el.classList.add('active');
        }

        widget.querySelector('b').innerHTML = data.rating;
    });

    return false;
};

/**
 * Добавление и удаление из избранного
 */
window.addFavorite = function (el) {
    post('/favorites/' + el.dataset.id, formData({
        csrf: el.dataset.csrf,
    })).then((data) => {
        if (! data.success) {
            message.error(data.message);

            return;
        }

        const icon = data.type === 'add' ? 'bi-heart-fill' : 'bi-heart';
        const count = parseInt(el.textContent, 10) + (data.type === 'add' ? 1 : -1);

        el.innerHTML = '<i class="bi ' + icon + '"></i> ' + count;

        message.success(data.message);
    });

    return false;
};
