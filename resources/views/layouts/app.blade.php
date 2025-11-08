<!DOCTYPE html><html lang="en"><head>
        
        <meta charset="utf-8">
        <title>Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin &amp; Dashboard Template" name="description">
        <meta content="Themesbrand" name="author">
        <!-- App favicon -->
        <link rel="shortcut icon" href="favicon.ico">

        <!-- Bootstrap Css -->
        <link href="{{ asset('/assets/') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <!-- Icons Css -->
        <link href="{{ asset('/assets/') }}/css/icons.min.css" rel="stylesheet" type="text/css">
        <!-- App Css-->
        <link href="{{ asset('/assets/') }}/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">
        
        <link href="{{ asset('/assets/') }}/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/assets/') }}/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="{{ asset('/assets/') }}/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />     
        <!-- App js -->
        @stack('css')
        <style>
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #f8f9fa; /* Optional styling */
                padding: 1rem;
                text-align: center;
        }
        .logo-styl{
            font-size: 28px;
            color: white;
            font-weight: bold;
            font-family: 'Font Awesome 5 Free';
        }
        .sub-menu.mm-show {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
        }
        </style>
    </head>

    <body data-sidebar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

            
            @include('layouts.topbar');
            @include('layouts.sidebar');
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        @yield('content')

                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->


               

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12" style="font-size: 15px;color: black;">
                            Design &amp; Develop by © <a href="https://www.facebook.com/profile.php?id=100088086532924" target="_blank" rel="noopener noreferrer" style="font-size: 15px;color: black;">Nakib</a>.
                            </div>
                            <!-- <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                <script>document.write(new Date().getFullYear())</script>
                                    <a href="https://www.facebook.com/profile.php?id=100088086532924" target="_blank" rel="noopener noreferrer">Nakib</a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- JAVASCRIPT -->
        <script src="{{ asset('/assets/') }}/js/jquery.min.js"></script>
        <script src="{{ asset('/assets/') }}/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('/assets/') }}/js/metisMenu.min.js"></script>
        <script src="{{ asset('/assets/') }}/js/simplebar.min.js"></script>
        <script src="{{ asset('/assets/') }}/js/waves.min.js"></script>

        <!-- dashboard init -->
        <script src="{{ asset('/assets/') }}/js/dashboard.init.js"></script>

        <!-- Required datatable js -->
        <script src="{{ asset('/assets/') }}/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="{{ asset('/assets/') }}/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/jszip/jszip.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="{{ asset('/assets/') }}/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        
        <!-- Responsive examples -->
        <script src="{{ asset('/assets/') }}/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="{{ asset('/assets/') }}/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

        <!-- Datatable init js -->
        <script src="{{ asset('/assets/') }}/js/datatables.init.js"></script>    
        <!-- App js -->
        <script src="{{ asset('/assets/') }}/js/app.js"></script>
    
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const currentUrl = window.location.href;
                const menuItems = document.querySelectorAll('#side-menu a');

                menuItems.forEach(item => {
                    if (item.href === currentUrl) {
                        item.classList.add('active'); // Highlight the current item

                        let parent = item.closest('li');
                        while (parent) {
                            if (parent.classList.contains('has-arrow')) {
                                parent.classList.add('mm-active');
                                const submenu = parent.querySelector('.sub-menu');
                                if (submenu) {
                                    submenu.classList.add('mm-show'); // Ensure submenu is visible
                                    submenu.style.height = `${submenu.scrollHeight}px`; // Set proper height
                                    parent.setAttribute('aria-expanded', 'true');
                                }
                            }
                            parent = parent.parentElement.closest('li'); // Move up the hierarchy
                        }
                    }
                });
            });

            $(document).ready(function () {
                    $('#side-menu').metisMenu(); // Initialize or re-initialize the menu
                });

        </script>
</body>
</html>