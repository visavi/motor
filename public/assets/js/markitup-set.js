// ----------------------------------------------------------------------------
// markItUp bb-code setting!
// ----------------------------------------------------------------------------
mySettings = {
    nameSpace: 'markItUpBlock',
    previewParserPath: '/bbcode', // path to your BBCode parser
    previewAutoRefresh: false,
    onTab: {keepDefault: false, openWith: '    '},
    markupSet: [
        {title: 'Жирный текст', name: '<i class="bi bi-type-bold"></i>', className: 'bb-bold', key: 'B', openWith: '[b]', closeWith: '[/b]'},
        {title: 'Наклонный текст', name: '<i class="bi bi-type-italic"></i>', className: 'bb-italic', key: 'I', openWith: '[i]', closeWith: '[/i]'},
        {title: 'Подчеркнутый текст', name: '<i class="bi bi-type-underline"></i>', className: 'bb-underline', key: 'U', openWith: '[u]', closeWith: '[/u]'},
        {title: 'Зачеркнутый текст', name: '<i class="bi bi-type-strikethrough"></i>', className: 'bb-strike', key: 'S', openWith: '[s]', closeWith: '[/s]'},

        {separator: '---------------'},
        {title: 'Ссылка', name: '<i class="bi bi-link"></i>', className: 'bb-link', key: 'L', openWith: '[url=[![' + 'Ссылка' + ':!:https://]!]]', closeWith: '[/url]', placeHolder: 'Текст ссылки...'},

        {title: 'Изображение', name: '<i class="bi bi-image"></i>', className: 'bb-image', openWith: '[img][![' + 'URL изображения' + ':!:https://]!]', closeWith: '[/img]'},

        {title: 'Видео', name: '<i class="bi bi-play-btn"></i>', className: 'bb-youtube', openWith: '[youtube][![' + 'Ссылка на видео с youtube' + ':!:https://]!]', closeWith: '[/youtube]'},
        {title: 'Цвет', name: '<i class="bi bi-palette"></i>', className: 'bb-color', openWith: '[color=[![' + 'Код цвета' + ']!]]', closeWith: '[/color]',
        dropMenu: [
            {name: 'Yellow', openWith: '[color=#ffd700]', closeWith: '[/color]', className: 'col1-1'},
            {name: 'Orange', openWith: '[color=#ffa500]', closeWith: '[/color]', className: 'col1-2'},
            {name: 'Red', openWith: '[color=#ff0000]', closeWith: '[/color]', className: 'col1-3'},

            {name: 'Blue', openWith: '[color=#0000ff]', closeWith: '[/color]', className: 'col2-1'},
            {name: 'Purple', openWith: '[color=#800080]', closeWith: '[/color]', className: 'col2-2'},
            {name: 'Green', openWith: '[color=#00cc00]', closeWith: '[/color]', className: 'col2-3'},

            {name: 'Magenta', openWith: '[color=#ff00ff]', closeWith: '[/color]', className: 'col3-1'},
            {name: 'Gray', openWith: '[color=#808080]', closeWith: '[/color]', className: 'col3-2'},
            {name: 'Cyan', openWith: '[color=#00ffff]', closeWith: '[/color]', className: 'col3-3'}
        ]},

        {separator: '---------------'},
        {title: 'Размер текста', name: '<i class="bi bi-type"></i>', className: 'bb-size', openWith: '[size=[![' + 'Размер текста от 1 до 5' + ']!]]', closeWith: '[/size]',
        dropMenu :[
            {name: 'x-small', openWith: '[size=1]', closeWith: '[/size]'},
            {name: 'small', openWith: '[size=2]', closeWith: '[/size]'},
            {name: 'medium', openWith: '[size=3]', closeWith: '[/size]'},
            {name: 'large', openWith: '[size=4]', closeWith: '[/size]'},
            {name: 'x-large', openWith: '[size=5]', closeWith: '[/size]'}
        ]},

        {title: 'По центру', name: '<i class="bi bi-text-center"></i>', className: 'bb-center', openWith: '[center]', closeWith: '[/center]'},
        {title: 'Спойлер', name: '<i class="bi bi-plus-lg"></i>', className: 'bb-spoiler', openWith: '[spoiler=[![' + 'Заголовок спойлера' + ']!]]', closeWith: '[/spoiler]', placeHolder: 'Текст спойлера...'},
        {
            title: 'Стикер',
            name: '<i class="bi bi-emoji-smile"></i>',
            className: 'bb-sticker',
            beforeInsert: function () {
                const stickerModal = $('#stickersModal');

                if (stickerModal.length) {
                    stickerModal.modal('show');

                    return false;
                }

                $.ajax({
                    dataType: 'json', type: 'get', url: '/stickers/modal',
                    success: function (data) {
                        if (data.success) {
                            $('body').append(data.stickers);
                        }

                        $('#stickersModal').modal('show')
                    }
                });
            }
        },

        {separator: '---------------'},
        {title: 'Скрытый контент', name: '<i class="bi bi-eye-slash"></i>', className: 'bb-hide', openWith: '[hide]', closeWith: '[/hide]'},
        {title: 'Цитата', name: '<i class="bi bi-chat-quote"></i>', className: 'bb-quote', openWith: '[quote]', closeWith: '[/quote]'},
        {title: 'Исходный код', name: '<i class="bi bi-code-slash"></i>', className: 'bb-code', openWith: '[code]', closeWith: '[/code]'},

        {separator: '---------------'},
        {title: 'Маркированный список', name: '<i class="bi bi-list-ul"></i>', className: 'bb-unorderedlist', multiline:true, openBlockWith: '[list]\n', closeBlockWith: '\n[/list]', placeHolder: 'Элемент списка'},
        {title: 'Нумерованный список', name: '<i class="bi bi-list-ol"></i>', className: 'bb-orderedlist', multiline:true, openBlockWith: '[list=1]\n', closeBlockWith: '\n[/list]', placeHolder: 'Элемент списка'},

        {separator: '---------------'},
        {title: 'Очистка тегов', name: '<i class="bi bi-eraser"></i>', className: 'bb-clean', replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)]/g, '') } },
        {title: 'Обрезка страницы', name: '<i class="bi bi-scissors"></i>', className: 'bb-cutpage', openWith: '[cut]'},
        {title: 'Просмотр', name: '<i class="bi bi-check2"></i>', classname: 'bb-preview',  call: 'preview'}
    ]
};
