<?php if (isset(session()->flash['errors'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php foreach (session()->flash['errors'] as $error): ?>
            <div><?= $this->e($error) ?></div>
        <?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset(session()->flash['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div><?= $this->e(session()->flash['success']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
