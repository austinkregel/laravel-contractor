@extends('spark::layouts.app')
@section('content')
    <?php
            $relationship = config('kregel.contractor.user_relationship');
            $name = config('kregel.contractor.related_model_name_key');
            $totalContracts = 0;
            $dueContracts = 0;
            $dueToday= 0;
            $dueTomorrow = 0;
            $currentDate = date('Y-m-d', strtotime('now'));
        foreach($relationship() as $ship){
            $totalContracts += $ship->contracts->count();
            $dueContracts += $ship->contracts()->whereRaw('MONTH(ended_at) = ? and DAY(ended_at) > ?', [date('m', strtotime('now')), date('d', strtotime('now'))])->get()->count();
            $dueToday += $ship->contracts()->whereRaw('MONTH(ended_at) = ? and DAY(ended_at) = ?', [date('m', strtotime('now')), date('d', strtotime('now'))])->get()->count();
            $dueTomorrow += $ship->contracts()->whereRaw('MONTH(ended_at) = ? and DAY(ended_at) = ?', [date('m', strtotime('now')), 1+date('d', strtotime('now'))])->get()->count();
            $dueContractsList[$ship->$name] = $ship->contracts()->whereRaw('MONTH(ended_at) = ? and DAY(ended_at) > ?', [date('m', strtotime('now')), date('d', strtotime('now'))])->get();
        }
    ?>

    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('contractor::shared.menu')
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Total Contracts: {{ $totalContracts }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Due this month: {{ $dueContracts }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Due Today: {{ $dueToday }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Due Tomorrow: {{ $dueToday }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection