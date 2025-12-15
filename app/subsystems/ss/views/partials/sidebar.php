<?php
$tipoUsuario = $_SESSION['tipo_usuario'] ?? null;
$isAdmin = $tipoUsuario === 'admin';
$isCadastrador = $tipoUsuario === 'cadastrador';
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

$selfPath = $_SERVER['PHP_SELF'] ?? '';
$isInViews = strpos($selfPath, '/views/') !== false;
$assetBase = $isInViews ? '../assets' : 'assets';
$viewsBase = $isInViews ? '' : 'views/';
$indexHref = $isInViews ? '../index.php' : 'index.php';

$activeBg = 'bg-white/10';
$inactiveBg = '';
$activeIconBg = 'bg-secondary';
$inactiveIconBg = 'bg-white/10';

function sidebar_is_active(string $currentPage, string $target): bool {
    return $currentPage === $target;
}
?>

<style>
    :root {
        --primary: #005A24;
        --secondary: #FFA500;
        --accent: #E6F4EA;
        --dark: #1A3C34;
        --light: #F8FAF9;
    }

    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        backdrop-filter: blur(10px);
        background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
        z-index: 50;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 100vw;
        max-width: 20rem;
    }

    .sidebar.open {
        transform: translateX(0);
    }

    .overlay {
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        backdrop-filter: blur(2px);
        z-index: 45;
    }

    .overlay.show {
        opacity: 1;
        visibility: visible;
    }

    @media (min-width: 1024px) {
        .sidebar {
            width: 20rem;
            position: static;
            flex-shrink: 0;
            transform: translateX(0);
        }

        .main-content {
            flex: 1;
            min-width: 0;
            margin-left: 0;
            overflow-x: hidden;
        }

        body {
            overflow-x: hidden;
        }
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: var(--dark);
    }

    .btn-animate {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .btn-animate::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }

    .btn-animate:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-animate:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-animate:active {
        transform: translateY(0);
    }

    .focus-ring:focus {
        outline: 2px solid var(--secondary);
        outline-offset: 2px;
    }

    .nav-item {
        position: relative;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border-radius: 12px;
    }

    .nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--secondary);
        border-radius: 0 4px 4px 0;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .nav-item:hover::before {
        transform: scaleY(1);
    }

    .nav-item:hover {
        transform: translateX(8px);
        background: rgba(255, 255, 255, 0.1);
    }

    #sidebar nav > :not([hidden]) ~ :not([hidden]) {
        margin-top: 0.25rem !important;
    }

    #sidebar .nav-item {
        padding: 0.5rem 0.75rem !important;
    }

    #sidebar .nav-item span {
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
    }

    #sidebar .nav-item p {
        display: none !important;
    }

    #sidebar .nav-item > div:first-child {
        width: 2.5rem !important;
        height: 2.5rem !important;
        margin-right: 0.75rem !important;
    }

    #sidebar .nav-item svg {
        width: 1.25rem !important;
        height: 1.25rem !important;
    }
</style>

<div id="overlay" class="overlay fixed inset-0 bg-black/30 z-40 lg:hidden"></div>

<aside id="sidebar" class="sidebar fixed left-0 top-0 h-screen w-80 shadow-2xl z-50 lg:translate-x-0 lg:static lg:z-auto custom-scrollbar overflow-y-auto">
    <div class="p-4">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/20">
            <div class="animate-slide-in-left">
                <div class="flex items-center space-x-2">
                    <img src="<?= $assetBase ?>/Brasão_do_Ceará.svg.png" alt="Brasão do Ceará" class="w-6 h-7 transition-transform hover:scale-105">
                    <h2 class="text-white text-lg font-bold font-display">Sistema Seleção</h2>
                </div>
            </div>
            <button id="closeSidebar" class="text-white lg:hidden btn-animate p-2 rounded-xl hover:bg-white/10 focus-ring" type="button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="space-y-2">
            <?php if ($isAdmin || $isCadastrador) { ?>
                <?php $isActive = sidebar_is_active($currentPage, 'index.php'); ?>
                <div>
                    <a href="<?= $indexHref ?>" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H14.25a.75.75 0 01-.75-.75v-6a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v6a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Início</span>
                            <p class="text-green-200 text-xs mt-1">Página inicial</p>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php if ($isAdmin) { ?>
                <?php $isActive = sidebar_is_active($currentPage, 'usuario.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.2s;">
                    <a href="<?= $viewsBase ?>usuario.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Usuários</span>
                            <p class="text-green-200 text-xs mt-1">Controle de acesso</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'cursos.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.25s;">
                    <a href="<?= $viewsBase ?>cursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Cursos</span>
                            <p class="text-green-200 text-xs mt-1">Administrar cursos</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'cotas.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.3s;">
                    <a href="<?= $viewsBase ?>cotas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 10-10 0v2M5 9h14l-1 11H6L5 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Cotas</span>
                            <p class="text-green-200 text-xs mt-1">Regras e perfis</p>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php if ($isAdmin || $isCadastrador) { ?>
                <?php $isActive = sidebar_is_active($currentPage, 'candidatos.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.35s;">
                    <a href="<?= $viewsBase ?>candidatos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-4-4h-1m-4 5H3v-1a4 4 0 014-4h6a4 4 0 014 4v1zm-1-9a4 4 0 10-8 0 4 4 0 008 0zm6 1a3 3 0 10-6 0 3 3 0 006 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Candidatos</span>
                            <p class="text-green-200 text-xs mt-1">Gerenciar inscrições</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'solicitar_alteracao.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.4s;">
                    <a href="<?= $viewsBase ?>solicitar_alteracao.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Requisições</span>
                            <p class="text-green-200 text-xs mt-1">Alteração de candidato</p>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php if ($isAdmin) { ?>
                <?php $isActive = sidebar_is_active($currentPage, 'relatorios.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.45s;">
                    <a href="<?= $viewsBase ?>relatorios.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Relatórios</span>
                            <p class="text-green-200 text-xs mt-1">Gerar documentos</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'recursos.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.42s;">
                    <a href="<?= $viewsBase ?>recursos.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Recursos</span>
                            <p class="text-green-200 text-xs mt-1">Gerenciar recursos</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'matriculas.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.47s;">
                    <a href="<?= $viewsBase ?>matriculas.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Matrículas</span>
                            <p class="text-green-200 text-xs mt-1">Gerenciar dias e horários</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'perfil_escola.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.53s;">
                    <a href="<?= $viewsBase ?>perfil_escola.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l9 4 9-4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Perfil Escola</span>
                            <p class="text-green-200 text-xs mt-1">Dados e foto da escola</p>
                        </div>
                    </a>
                </div>

                <?php $isActive = sidebar_is_active($currentPage, 'limpar_banco.php'); ?>
                <div class="animate-slide-in-left" style="animation-delay: 0.5s;">
                    <a href="<?= $viewsBase ?>limpar_banco.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-red-500 group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-base">Limpar Banco</span>
                            <p class="text-green-200 text-xs mt-1">Resetar dados</p>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php $isActive = sidebar_is_active($currentPage, 'faq.php'); ?>
            <div class="animate-slide-in-left" style="animation-delay: 0.55s;">
                <a href="<?= $viewsBase ?>faq.php" class="nav-item flex items-center px-4 py-4 text-white hover:text-white transition-all group focus-ring <?= $isActive ? $activeBg : $inactiveBg ?>">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all duration-300 <?= $isActive ? $activeIconBg : $inactiveIconBg ?>">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-base">FAQ</span>
                        <p class="text-green-200 text-xs mt-1">Dúvidas frequentes</p>
                    </div>
                </a>
            </div>
        </nav>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if (openSidebar) {
            openSidebar.addEventListener('click', () => {
                sidebar?.classList.add('open');
                overlay?.classList.add('show');
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('show');
            });
        }
    });
</script>
