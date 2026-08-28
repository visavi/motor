<?php

use App\Models\User;
use App\Repositories\NotificationRepository;

?>
<header class="navbar navbar-expand-md d-print-none sticky-top">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-label="Меню">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a href="/" class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <svg width="32" height="32" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="me-2" aria-hidden="true">
                <path fill="currentColor" d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"/>
                <path fill="currentColor" d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"/>
            </svg>
            Motor CMS
        </a>

        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex align-items-center me-2">
                <form class="input-icon" action="/search">
                    <input name="search" type="search" class="form-control form-control-sm" placeholder="Поиск&hellip;" aria-label="Поиск" required>
                    <span class="input-icon-addon">
                        <i class="bi bi-search"></i>
                    </span>
                </form>
            </div>

            <div class="nav-item dropdown d-none d-md-flex me-2">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" aria-label="Тема оформления" data-bs-display="static" id="bd-theme">
                    <i class="bi bi-circle-half"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <button type="button" class="dropdown-item" data-bs-theme-value="light">
                        <i class="bi bi-sun me-2"></i> Светлая
                    </button>
                    <button type="button" class="dropdown-item" data-bs-theme-value="dark">
                        <i class="bi bi-moon-stars me-2"></i> Тёмная
                    </button>
                    <button type="button" class="dropdown-item" data-bs-theme-value="auto">
                        <i class="bi bi-circle-half me-2"></i> Авто
                    </button>
                </div>
            </div>

            <?php if (isUser()): ?>
                <?php $notificationCount = (new NotificationRepository())->getCount(); ?>
                <div class="d-none d-md-flex me-2">
                    <a href="<?= route('notifications') ?>" class="nav-link px-0 position-relative" aria-label="Уведомления">
                        <i class="bi bi-bell"></i>
                        <?php if ($notificationCount): ?>
                            <span class="badge bg-red text-white badge-notification"><?= $notificationCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Меню пользователя">
                        <?= getUser()->getAvatar() ?>
                        <div class="d-none d-xl-block ps-2">
                            <div><?= escape(getUser()->getName()) ?></div>
                            <div class="mt-1 small text-secondary"><?= getUser()->getRole() ?></div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="<?= route('user', ['login' => getUser('login')]) ?>" class="dropdown-item">Профиль</a>
                        <a href="<?= route('user-stories', ['login' => getUser('login')]) ?>" class="dropdown-item">Мои статьи</a>
                        <a href="<?= route('favorites') ?>" class="dropdown-item">Избранное</a>
                        <a href="<?= route('notifications') ?>" class="dropdown-item d-md-none">
                            Уведомления
                            <?php if ($notificationCount): ?>
                                <span class="badge bg-red-lt ms-auto"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </a>

                        <?php if (isAdmin(User::BOSS)): ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= route('admin') ?>" class="dropdown-item">Админ-панель</a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>
                        <a href="<?= route('logout') ?>" class="dropdown-item" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-confirm="Вы подтверждаете выход?">Выход</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="btn-list">
                    <a href="<?= route('login') ?>" class="btn btn-sm">Войти</a>
                    <a href="<?= route('register') ?>" class="btn btn-sm btn-primary">Регистрация</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav">
                <li class="nav-item<?= str_starts_with(currentRoute(), route('stories')) ? ' active' : '' ?>">
                    <a class="nav-link" href="<?= route('stories') ?>">
                        <span class="nav-link-icon"><i class="bi bi-card-heading"></i></span>
                        <span class="nav-link-title">Статьи</span>
                    </a>
                </li>
                <li class="nav-item<?= str_starts_with(currentRoute(), route('guestbook')) ? ' active' : '' ?>">
                    <a class="nav-link" href="<?= route('guestbook') ?>">
                        <span class="nav-link-icon"><i class="bi bi-chat-square-text"></i></span>
                        <span class="nav-link-title">Гостевая</span>
                    </a>
                </li>
                <li class="nav-item<?= str_starts_with(currentRoute(), route('users')) ? ' active' : '' ?>">
                    <a class="nav-link" href="<?= route('users') ?>">
                        <span class="nav-link-icon"><i class="bi bi-people"></i></span>
                        <span class="nav-link-title">Пользователи</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tags">
                        <span class="nav-link-icon"><i class="bi bi-tags"></i></span>
                        <span class="nav-link-title">Теги</span>
                    </a>
                </li>

                <?php if (isUser() && (setting('story.allow_posting') || isAdmin())): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= route('story-create') ?>">
                            <span class="nav-link-icon"><i class="bi bi-plus-circle"></i></span>
                            <span class="nav-link-title">Добавить статью</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-md-none mt-2">
                <form class="input-icon" action="/search">
                    <input name="search" type="search" class="form-control" placeholder="Поиск&hellip;" aria-label="Поиск" required>
                    <span class="input-icon-addon">
                        <i class="bi bi-search"></i>
                    </span>
                </form>
            </div>
        </div>
    </div>
</header>
