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

    /*var hash = $(location).attr('hash');

    if (hash.match("^#comment_")) {
        postReply($(hash));
    }*/
});

sendComment = function (el){
    let form = $(el).closest('.js-form');
    let url = form.find('form').attr('action');

    let formData = {
        text: form.find('textarea[name="text"]').val(),
        csrf: form.find('input[name="csrf"]').val(),
        parent_id: form.find('input[name="parent_id"]').val(),
    };

    $.ajax({
        type: 'post',
        url: url,
        data: formData,
        dataType: 'json',
        encode: true,
        success: function (data) {
            if (data.success) {
                let currentPost = form.closest('.post');
                let newPost = $('.js-post').clone().removeClass('js-post').show();
                let depth = parseInt(currentPost.data('depth')) + 1;

                newPost.find('.post-message').html(data.comment.text);
                newPost.find('.post-date').text(data.comment.created_at);
                newPost.attr('id', 'comment_' + data.comment.id).css('margin-left', depth * 20 + 'px');

                newPost.insertAfter(currentPost);
                form.remove();

                toastr.success(data.message);
            } else {
                form.find('textarea[name="text"]').addClass('is-invalid');
                form.find('.invalid-feedback').text(data.message).css('display', 'block');

                toastr.error(data.message);
            }
        }
    });

    return false;
};

/* Переход к форме ввода */
postJump = function () {
    $('html, body').animate({
        scrollTop: ($('.post-form').offset().top)
    }, 100);
};

/* Ответ на сообщение */
postReply = function (el) {
    $('.js-answer').remove();

    var post = $(el).closest('.post');
    var form = $('.js-form').clone();

    var commentId = post.attr('id').replace(/comment_/, '');
    form.find("[name='parent_id']").val(commentId);

    form.find('textarea').markItUp(mySettings);
    form.addClass('js-answer').show();

    $(el).closest('.post').append(form);

    var author = post.find('.post-author').data('login');
    form.focus().find('.markItUpEditor').val(author + ', ');

    return false;
};

/* Цитирование сообщения */
postQuote = function (el) {
    $('.js-answer').remove();

    var post = $(el).closest('.post');
    var form = $('.js-form').clone();

    var commentId = post.attr('id').replace(/comment_/, '');
    form.find("[name='parent_id']").val(commentId);

    form.find('textarea').markItUp(mySettings);
    form.addClass('js-answer').show();

    $(el).closest('.post').append(form);

    var author  = post.find('.post-author').data('login');
    var date    = post.find('.post-date').text();
    var text    = post.find('.post-message').clone();
    var message = $.trim(text.find('blockquote').remove().end().text());

    form.focus().find('.markItUpEditor').val('[quote=' + author + ' ' + date + ']' + message + '[/quote]\n');

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

/* Загрузка файла */
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

                    pasteImage(template);
                } else {
                    var template = $('.js-file-template').clone();

                    template.find('.js-file-link').attr({
                        'href': data.path
                    }).text(data.name);

                    template.find('.js-file-size').text(data.size);
                }

                template.find('.js-file-delete').attr('data-id', data.id);
                $('.js-files').append(template.html());
            }
        }
    });

    el.value = ''

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

/* Добавление / удаление из избранного */
addFavorite = function (el) {
    $.ajax({
        data: {
            csrf: $(el).data('csrf')
        },
        dataType: 'json',
        type: 'post',
        url: '/favorites/' + $(el).data('id'),
        success: function (data) {

            if (! data.success) {
                toastr.error(data.message);
                return false;
            }

            if (data.success) {
                if (data.type === 'add') {
                    toastr.success(data.message);
                    const icon = '<i class="bi bi-heart-fill"></i>';
                    const countFavorites = parseInt($(el).text()) + 1;
                    $(el).html(icon + ' ' + countFavorites);
                }

                if (data.type === 'delete') {
                    toastr.success(data.message);
                    const icon = '<i class="bi bi-heart"></i>';
                    const countFavorites = parseInt($(el).text()) - 1;
                    $(el).html(icon + ' ' + countFavorites);
                }
            }
        }
    });

    return false;
};
