<?php if (session()->has('flash')): ?>
    <?php $flash = session()->get('flash'); ?>
    <?php if (isset($flash['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php foreach ($flash['errors'] as $error): ?>
                <div><?= $this->e($error) ?></div>
            <?php endforeach; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($flash['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div><?= $this->e($flash['success']) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php session()->delete('flash'); ?>
<?php endif; ?>
