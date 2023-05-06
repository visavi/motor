<?php

use App\Models\User;

?>
<header class="navbar navbar-expand-lg bd-navbar sticky-top">
    <nav class="container-xxl bd-gutter flex-wrap flex-lg-nowrap" aria-label="Main navigation">
        <div class="d-lg-none" style="width: 2.25rem;"></div>
        <a class="navbar-brand logo p-0 me-0 me-lg-2" href="/">
            <svg class="" width="40px" height="40px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z"/><path fill="#fff" d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z"/></svg>

            <span class="fw-bold fs-4 align-middle" href="/">MOTOR CMS</span>
        </a>
        <button class="navbar-toggler d-flex d-lg-none order-3 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#bdNavbar" aria-controls="bdNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
        </button>
        <div class="offcanvas-lg offcanvas-end flex-grow-1" tabindex="-1" id="bdNavbar" aria-labelledby="bdNavbarOffcanvasLabel" data-bs-scroll="true">
            <div class="offcanvas-header px-4 pb-0">
                <h5 class="offcanvas-title text-white" id="bdNavbarOffcanvasLabel">MOTOR CMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#bdNavbar"></button>
            </div>
            <div class="offcanvas-body p-4 pt-0 p-lg-0">
                <hr class="d-lg-none text-white-50">
                <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
                    <li class="nav-item col-6 col-lg-auto">
                        <a class="nav-link py-2 px-0 px-lg-2<?= str_starts_with(currentRoute(), route('stories')) ? ' active' : '' ?>" href="<?= route('stories') ?>">Статьи</a>
                    </li>
                    <li class="nav-item col-6 col-lg-auto">
                        <a class="nav-link py-2 px-0 px-lg-2<?= str_starts_with(currentRoute(), route('guestbook')) ? ' active' : '' ?>" href="<?= route('guestbook') ?>">Гостевая</a>
                    </li>
                    <li class="nav-item col-6 col-lg-auto">
                        <a class="nav-link py-2 px-0 px-lg-2<?= str_starts_with(currentRoute(), route('users')) ? ' active' : '' ?>" href="<?= route('users') ?>">Пользователи</a>
                    </li>
                </ul>

                <ul class="navbar-nav flex-row flex-wrap ms-md-auto">
                    <li class="nav-item col-12 col-lg-auto">
                        <form class="js-form-search app-search" action="/search">
                            <input name="search" class="app-search-input form-control" type="search" placeholder="Поиск..." aria-label="Search" required>
                            <button class="app-search-button"><i class="bi bi-search"></i></button>
                        </form>
                    </li>
                </ul>

                <hr class="d-lg-none text-white-50">
                <ul class="navbar-nav flex-row flex-wrap ms-md-auto">
                    <li class="nav-item col-6 col-lg-auto">
                        <a class="nav-link py-2 px-0 px-lg-2" href="https://github.com/visavi/motor" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 499.36" role="img"><title>GitHub</title><path fill="currentColor" fill-rule="evenodd" d="M256 0C114.64 0 0 114.61 0 256c0 113.09 73.34 209 175.08 242.9 12.8 2.35 17.47-5.56 17.47-12.34 0-6.08-.22-22.18-.35-43.54-71.2 15.49-86.2-34.34-86.2-34.34-11.64-29.57-28.42-37.45-28.42-37.45-23.27-15.84 1.73-15.55 1.73-15.55 25.69 1.81 39.21 26.38 39.21 26.38 22.84 39.12 59.92 27.82 74.5 21.27 2.33-16.54 8.94-27.82 16.25-34.22-56.84-6.43-116.6-28.43-116.6-126.49 0-27.95 10-50.8 26.35-68.69-2.63-6.48-11.42-32.5 2.51-67.75 0 0 21.49-6.88 70.4 26.24a242.65 242.65 0 0 1 128.18 0c48.87-33.13 70.33-26.24 70.33-26.24 14 35.25 5.18 61.27 2.55 67.75 16.41 17.9 26.31 40.75 26.31 68.69 0 98.35-59.85 120-116.88 126.32 9.19 7.9 17.38 23.53 17.38 47.41 0 34.22-.31 61.83-.31 70.23 0 6.85 4.61 14.81 17.6 12.31C438.72 464.97 512 369.08 512 256.02 512 114.62 397.37 0 256 0z"></path></svg>
                            <span class="d-lg-none ms-2">GitHub</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                            <symbol id="circle-half" viewBox="0 0 16 16">
                                <path fill="currentColor" fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"></path>
                            </symbol>
                            <symbol id="moon-stars-fill" viewBox="0 0 16 16">
                                <path fill="currentColor" fill-rule="evenodd" d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"></path>
                                <path fill="currentColor" fill-rule="evenodd" d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"></path>
                            </symbol>
                            <symbol id="sun-fill" viewBox="0 0 16 16">
                                <path fill="currentColor" fill-rule="evenodd" d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"></path>
                            </symbol>
                        </svg>

                        <a href="#" class="nav-link d-block text-decoration-none dropdown-toggle" id="bd-theme" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                            <svg class="theme-icon-active" width="20" height="20"><use href="#sun-fill"></use></svg>
                            <span class="d-lg-none ms-2">Тема</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme" style="--bs-dropdown-min-width: 8rem;">
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="light">
                                    <svg class="bi me-2 opacity-50 theme-icon"><use href="#sun-fill"></use></svg>
                                    Светлая
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                                    <svg class="bi me-2 opacity-50 theme-icon"><use href="#moon-stars-fill"></use></svg>
                                    Темная
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                                    <svg class="bi me-2 opacity-50 theme-icon"><use href="#circle-half"></use></svg>
                                    Авто
                                </button>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
                        <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
                        <hr class="d-lg-none my-2 text-white-50">
                    </li>

                    <?php if (isUser()): ?>
                        <?php if(setting('story.allow_posting') || isAdmin()): ?>

                            <li class="nav-item col-6 col-lg-auto">
                                <a class="nav-link py-2 px-0 px-lg-2" href="<?= route('story-create') ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                    </svg>
                                    <span class="d-lg-none ms-2">Добавить статью</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link d-block text-decoration-none dropdown-toggle" id="bd-profile" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="avatar-micro"><?= getUser()->getAvatar() ?></span>
                                <span class="d-lg-none ms-2"><?= getUser()->getName() ?></span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-profile" style="--bs-dropdown-min-width: 8rem;">
                                <li><a class="dropdown-item" href="<?= route('user', ['login' => getUser('login')]) ?>">Профиль</a></li>

                                <li><a class="dropdown-item" href="<?= route('user-stories', ['login' => getUser('login')]) ?>">Мои статьи</a></li>
                                <li><a class="dropdown-item" href="<?= route('favorites') ?>">Избранное</a></li>

                                <?php if (isAdmin(User::BOSS)): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= route('admin-settings') ?>">Настройки сайта</a></li>
                                <?php endif; ?>

                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= route('logout') ?>" onclick="return submitForm(this);" data-csrf="<?= session('csrf') ?>" data-confirm="Вы подтверждаете выход?">Выход</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item col-6 col-lg-auto">
                            <a class="nav-link py-2 px-0 px-lg-2<?= str_starts_with(currentRoute(), route('login')) ? ' active' : '' ?>" href="<?= route('login') ?>">Войти</a>
                        </li>
                        <li class="nav-item col-6 col-lg-auto">
                            <a class="nav-link py-2 px-0 px-lg-2<?= str_starts_with(currentRoute(), route('register')) ? ' active' : '' ?>" href="<?= route('register') ?>">Регистрация</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
