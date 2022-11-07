<header class="p-3 mb-3 border-bottom shadow" style="background-color: #2e8cc2">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-light text-decoration-none">
                <svg width="40px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"/><path d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"/></svg>
                <span class="fs-2">Motor CMS</span>
            </a>

            <!--<ul class="nav col-12 col-md-auto me-md-auto mb-2 justify-content-center mb-md-0">-->
            <ul class="nav col-12 col-md-auto me-md-auto">
                <!--<li><a href="/guestbook" class="nav-link px-2 link-light">Гостевая</a></li>-->
            </ul>

            <form class="js-form-search app-search col-12 col-md-auto mb-3 mb-md-0 me-md-3" action="/search" style="display: none">
                <input name="search" class="app-search-input form-control" type="search" placeholder="Поиск..." aria-label="Search" required>
                <button class="app-search-button"><i class="bi bi-search"></i></button>
            </form>

            <div class="me-3" onclick="return openSearch(this);" style="font-size: 2rem; color: #fff">
                <a href="#"><i class="bi bi-search text-light"></i></a>
            </div>

            <?php if (isUser()): ?>
                <?php if(setting('story.allow_posting') || isAdmin()): ?>
                    <div class="me-3" style="font-size: 2rem; color: #fff" title="Добавить статью" data-bs-toggle="tooltip">
                        <a href="/create"><i class="bi bi-plus-circle text-light"></i></a>
                    </div>
                <?php endif; ?>

                <div class="dropdown text-end">
                    <a href="#" class="d-block link-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar-medium"><?= getUser()->getAvatar() ?></span>
                    </a>

                    <ul class="dropdown-menu text-small" style="">
                        <li><a class="dropdown-item" href="/users/<?= getUser('login') ?>">Профиль</a></li>

                        <li><a class="dropdown-item" href="/users/<?= getUser('login') ?>/stories">Мои статьи</a></li>
                        <li><a class="dropdown-item" href="/favorites">Избранное</a></li>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-confirm="Вы подтверждаете выход?">Выход</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="me-2 py-2 text-light" href="/login">Войти</a>
                <a class="me-2 py-2 text-light" href="/register">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</header>
