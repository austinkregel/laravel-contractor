@extends('spark::layouts.app')
@section('content')
<?php
        $contracts = ([]);
        foreach(auth()->user()->jurisdiction as $jur){
            $contracts[] = ($jur->contracts);
        }
        $contract = \Kregel\Contractor\Models\Contract::find(1);
        ?>
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default panel-flush">
                    <div class="panel-heading">
                        Add some contracts!
                    </div>
                    <div class="spark-panel-body panel-body">
                        <div class="spark-settings-tabs">
                            <ul class="nav-wrapper nav spark-settings-tabs-stacked" role="tablist">
                                @foreach(config('kregel.contractor.user_relationship') as $a)
                                <li class="dropdown submenu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                       aria-expanded="false">{{ $a }} <i class="fa fa-chevron-down pull-right"></i></a>
                                    <ul class="dropdown-menu">
                                        <!-- Settings -->
                                        <li>
                                            <a href="{{ route('warden::new-model', 'sdfsdf') }}" class="p-link">
                                                <i class="fa fa-btn fa-fw fa-cog"></i>New {{ ucwords('sdfsdf') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('warden::models', 'sdfsdf') }}" class="p-link">
                                                <i class="fa fa-btn fa-fw fa-cog"></i>List all {{ ucwords('asdfa') }}s
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Users</div>
                    <div class="panel-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection