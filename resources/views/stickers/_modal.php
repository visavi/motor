<?php /** @var array<string, string> $stickers */ ?>
<div class="modal fade" id="stickersModal" tabindex="-1" aria-labelledby="stickersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stickersModalLabel">Стикеры</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <?php foreach($stickers as $code => $path): ?>
                    <div class="col">
                        <a href="#" onclick="return pasteSticker(this);"><img src="<?= $path ?>" alt="<?= $code ?>" class="img-fluid" style="max-width: 50px;"></a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        pasteSticker = function (el) {
            var field = $('.markItUpEditor');
            var paste = $(el).find('img').attr('alt') + ' ';
            $('#stickersModal').modal('hide');
            field.focus().caret(paste);

            return false;
        };
    </script>
</div>
