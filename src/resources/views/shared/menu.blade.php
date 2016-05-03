<?php
$relationship = config('kregel.contractor.user_relationship');
$name = config('kregel.contractor.related_model_name_key');
?>
<div class="panel panel-default panel-flush">
        <div class="panel-heading">
            Add some contracts!
        </div>
        <div class="spark-panel-body panel-body">
            <div class="spark-settings-tabs">
                <ul class="nav-wrapper nav spark-settings-tabs-stacked" role="tablist" style="max-height:50rem;overflow:scroll;">

                    @foreach($relationship() as $ship)
                        <li class="dropdown submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                               aria-expanded="false">{{ $ship->$name }} <i class="fa fa-chevron-down pull-right"></i></a>
                            <ul class="dropdown-menu">
                                <!-- Settings -->
                                <li>
                                    <a href="{{ route('contractor::new', str_slug($ship->$name)) }}" class="p-link">
                                        <i class="fa fa-btn fa-fw fa-cog"></i>New contract
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('contractor::list', str_slug($ship->$name)  ) }}" class="p-link">
                                        <i class="fa fa-btn fa-fw fa-cog"></i>List all contracts
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>