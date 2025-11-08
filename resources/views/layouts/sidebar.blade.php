
            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar="" class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title" key="t-menu">Menu</li>
                            <li>
                                <a href="{{ route('dashboard') }}" class="waves-effect">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-dashboards">Dashboards</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact.index') }}" class="waves-effect">
                                    <i class="bx bx-chat"></i>
                                    <span key="t-chat">Contact</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                                    <i class="bx bx-group"></i>
                                    <span key="t-group">Patient Manage</span>
                                </a>
                                <ul class="sub-menu mm-collapse" aria-expanded="false" style="height: 0px;">
                                    <li><a href="{{route('patient.index')}}" key="t-tui-calendar">Patient List</a></li>
                                    <li><a href="{{route('without-invoce')}}" key="t-tui-calendar">Without Invoce</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('reference.index') }}" class="waves-effect">
                                    <i class="bx bx-user-plus"></i>
                                    <span key="t-user-plus">Reference</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                                    <i class="bx bx-calendar"></i>
                                    <span key="t-dashboards">Invoice Managment</span>
                                </a>
                                <ul class="sub-menu mm-collapse" aria-expanded="false" style="height: 0px;">
                                    <li><a href="{{route('invoice.index')}}" key="t-tui-calendar">Invoice List</a></li>
                                    <li><a href="{{route('due-invoice')}}" key="t-tui-calendar">Due Invoice</a></li>
                                    <li><a href="{{route('paid-invoice')}}" key="t-tui-calendar">Paid Invoice</a></li>
                                    <!-- <li><a href="calendar-full.html" key="t-full-calendar">Full Calendar</a></li> -->
                                </ul>
                            </li>
                            @if(Auth::user()->type == 1)
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                                    <i class="mdi mdi-cog-sync-outline"></i>
                                    <span key="t-sync-outline">Settings</span>
                                </a>
                                <ul class="sub-menu mm-collapse" aria-expanded="false" style="height: 0px;">
                                    <li><a href="{{route('medical-test.index')}}" key="t-tui-calendar">Medical Test</a></li>
                                    <li><a href="{{route('medical-center.index')}}" key="t-tui-calendar">Medical Center</a></li>
                                    <li><a href="{{route('wafid-slip.index')}}" key="t-tui-calendar">Wafid</a></li>
                                </ul>
                            </li>
                            @endif

                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->

