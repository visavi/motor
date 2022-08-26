$(function () {
    prettyPrint();

    toastr.options = {
        'toastClass' : 'toastr',
        'progressBar': true,
        'positionClass': 'toast-top-full-width'
    };

    $('.markItUp').markItUp(mySettings).on('input', function () {
        var maxlength = $(this).attr('maxlength');
        var text      = $(this).val().replace(/(\r\n|\n|\r)/g, "\r\n");

        var currentLength = text.length;
        var counter = $('.js-textarea-counter');

        if (currentLength > maxlength) {
            counter.addClass('text-danger');
        } else {
            counter.removeClass('text-danger');
        }

        counter.text('Осталось символов: ' + (maxlength - currentLength));

        if (currentLength === 0) {
            counter.empty();
        }
    });

    $('[data-bs-toggle="tooltip"]').tooltip();
    $('[data-bs-toggle="popover"]').popover();

    // Hide popover poppers anywhere
    $('body').on('click', function (e) {
        //did not click a popover toggle or popover
        if ($(e.target).data('bs-toggle') !== 'popover'
            && $(e.target).parents('.popover.in').length === 0) {
            $('[data-bs-toggle="popover"]').popover('hide');
        }
    });

    // Spoiler
    $('.spoiler-title').on('click', function () {
        var spoiler = $(this).parent();
        spoiler.toggleClass('spoiler-open');
        spoiler.find('.spoiler-text:first').slideToggle();
    });

    // Fix invalid markitup
    $('.markItUp .is-invalid')
        .closest('.markItUpBlock').parent()
        .find('.invalid-feedback').show();
});

/* Переход к форме ввода */
postJump = function () {
    $('html, body').animate({
        scrollTop: ($('.post').offset().top)
    }, 100);
};

/* Ответ на сообщение */
postReply = function (el) {
    postJump();

    var field  = $('.markItUpEditor');
    var post   = $(el).closest('.post');
    var author = post.find('.post-author').data('login');

    var $lastSymbol = field.val().slice(field.val().length - 1);
    var separ = $.inArray($lastSymbol, ['', '\n']) !== -1 ? '' : '\n';

    field.focus().val(field.val() + separ + author + ', ');

    return false;
};

/* Цитирование сообщения */
postQuote = function (el) {
    postJump();

    var field   = $('.markItUpEditor');
    var post    = $(el).closest('.post');
    var author  = post.find('.post-author').data('login');
    var date    = post.find('.post-date').text();
    var text    = post.find('.post-message').clone();
    var message = $.trim(text.find('blockquote').remove().end().text());

    var $lastSymbol = field.val().slice(field.val().length - 1);
    var separ = $.inArray($lastSymbol, ['', '\n']) !== -1 ? '' : '\n';

    if (!message) {
        field.focus().val(field.val() + separ + author + ', ');

        return false;
    }

    field.focus().val(field.val() + separ + '[quote=' + author + ' ' + date + ']' + message + '[/quote]\n');

    return false;
};

/* Отправляет скрытую форму */
submitForm = function (el) {
    if(! confirm($(el).data('confirm')  ?? 'Вы подтверждаете действие?')) {
        return false;
    }

    var form = $('<form action="' + $(el).attr('href') + '" method="POST"></form>')
    form.append('<input type="hidden" name="csrf" value="' + $(el).data('csrf') + '">');

    if ($(el).data('method')) {
        form.append('<input type="hidden" name="_METHOD" value="' + $(el).data('method').toUpperCase() + '">');
    }

    form.appendTo('body').submit();

    return false;
};

/* Вставка изображения в форму */
pasteImage = function (el) {
    var field = $('.markItUpEditor');
    var paste = '[img]' + $(el).find('img').attr('src') + '[/img]';

    field.focus().caret(paste);
};

/* Удаление изображения из формы */
cutImage = function (path) {
    var field = $('.markItUpEditor');
    var text  = field.val();
    var cut   = '[img]' + path + '[/img]';

    field.val(text.replace(cut, ''));
};

/* Загрузка изображения */
submitFile = function (el) {
    var form = new FormData();
    form.append('file', el.files[0]);
    form.append('id', $(el).data('id'));
    form.append('type', $(el).data('type'));
    form.append('csrf', $(el).data('csrf'));

    $.ajax({
        data: form,
        type: 'post',
        contentType: false,
        processData: false,
        dataType: 'json',
        url: '/upload',
        beforeSend: function () {
            $('.js-files').append('<i class="fas fa-spinner fa-spin fa-3x mx-3"></i>');
        },
        complete: function () {
            $('.fa-spinner').remove();
        },
        success: function (data) {
            if (! data.success) {
                toastr.error(data.message);
                return false;
            }

            if (data.success) {
                if (data.type === 'image') {
                    var template = $('.js-image-template').clone();

                    template.find('img').attr({
                        'src': data.path
                    });
                } else {
                    var template = $('.js-file-template').clone();

                    template.find('.js-file-link').attr({
                        'href': data.path
                    }).text(data.name);

                    template.find('.js-file-size').text(data.size);
                }

                template.find('.js-file-delete').attr('data-id', data.id);
                $('.js-files').append(template.html());

                pasteImage(template);
            }
        }
    });

    return false;
};

/* Удаление файла */
deleteFile = function (el) {
    var form = new FormData();
    form.append('type', $(el).data('type'));
    form.append('csrf', $(el).data('csrf'));
    form.append('_METHOD', 'DELETE');

    $.ajax({
        data: form,
        type: 'post',
        contentType: false,
        processData: false,
        dataType: 'json',
        url: '/upload/' + $(el).data('id'),
        success: function (data) {
            if (! data.success) {
                toastr.error(data.message);
                return false;
            }

            if (data.success) {
                cutImage(data.path);
                $(el).closest('.js-file').hide('fast');
            }
        }
    });

    return false;
};

/* Изменение рейтинга */
changeRating = function (el) {
    $.ajax({
        data: {
            type: $(el).data('type'),
            vote: $(el).data('vote'),
            csrf: $(el).data('csrf')
        },
        dataType: 'json',
        type: 'post',
        url: '/rating/' + $(el).data('id'),
        success: function (data) {
            if (data.success) {
                const rating = $(el).closest('.js-rating').find('b');

                $(el).closest('.js-rating').find('a').removeClass('active');

                if (! data.cancel) {
                    $(el).addClass('active');
                }

                rating.html($(data.rating));
            } else {
                if (data.message) {
                    toastr.error(data.message);
                }
                return false;
            }
        }
    });

    return false;
};
