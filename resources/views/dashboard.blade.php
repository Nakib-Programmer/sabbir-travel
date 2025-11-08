@extends('layouts.app')

@section('content')
<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body bg-secondary text-white" style="height: 116px; border-radius: 10px;">
                                                <a href="{{route('without-invoce')}}">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <h3 class="text-white fw-medium">WithOut Invoice</h3>
                                                            <h4 class="mb-0 text-white">{{ $withoutInvoice }}</h4>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body bg-danger text-white" style="height: 116px; border-radius: 10px;">
                                                <a href="{{ route('due-invoice') }}">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <h3 class="text-white fw-medium">Due Invoice</h3>
                                                            <h4 class="mb-0 text-white">{{$dueCount}}</h4>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body bg-info text-white" style="height: 116px; border-radius: 10px;">
                                                <a href="{{ route('paid-invoice') }}">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <h3 class="text-white fw-medium">Paid Invoice</h3>
                                                            <h4 class="mb-0 text-white">{{$paidCount}}</h4>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body text-white" style="height: 116px; border-radius: 10px;background: linear-gradient(0deg, rgb(48 60 121) 0%, #847d75 100%);">
                                                <a href="#">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <h3 class="text-white fw-medium">Due Invoice</h3>
                                                            <h4 class="mb-0 text-white">1,235</h4>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                        
@endsection

