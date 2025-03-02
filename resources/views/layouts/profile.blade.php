<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Поиск освобождающихся или дроп доменов</title>
        <meta name="description" content="Сервис поиска доменов, которые скоро могут освободиться. Дроп домены, у которых заканчивается срок регистрации" />

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
        <link rel="icon" href="/img/favicon.png" type="image/png">
    </head>
    <body>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <!-- <a class="navbar-brand ps-3" href="index.html">Start Bootstrap</a> -->
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
                <i class="bi bi-list"></i>
            </button>
            <!-- topinfo -->
            <div class="topinfo d-flex justify-content-center">
                <h3 style="font-weight: bold; letter-spacing: 1px;">DomainFree</h3>
            </div>
        </nav>

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <div class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion"></div>
            </div>

            <div id="layoutSidenav_content" style="min-height: auto;">
                <div class="container-fluid px-4">
                    <div class="row">
                        <div class="col-12 pt-4">
                            <h2>Поиск освобождающихся доменов ru, su, рф</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main id="app">
            @yield('content')
        </main>



{{--        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">--}}
{{--            @csrf--}}
{{--        </form>--}}

        <!-- Scripts -->
        <script src="{{ asset('js/main.js') }}" defer></script>
        <script src="{{ asset('js/add.js') }}" defer></script>
    </body>
</html>
