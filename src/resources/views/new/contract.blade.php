@extends('spark::layouts.app')
@section('content')
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-12">
                @include('warden::shared.errors')
            </div>
            <div class="col-md-4">
                @include('contractor::shared.menu')
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="vue-form-wrapper">
                            <div id="response" v-show="response" :class="responseClasses">
                                @{{ response }}
                                <div class="close" @click="close">&times;</div>
                        </div>
                        <form method="POST" enctype="multipart/form-data"
                              action="{{ route('contractor::post', str_slug($related_model->name)) }}">
                            <div class="form-group">
                                <input class="form-control" id="_redirect" type="hidden" name="_redirect"
                                       value="{{ route('contractor::home') }}">
                            </div>
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="name">Name the contract</label>
                                <input class="form-control" type="text" name="name">
                            </div>

                            <div class="form-group">
                                <label for="who_its_through">Who is this contract with? (The other party
                                    involved)</label>
                                <input class="form-control" type="text" name="who_its_through">
                            </div>

                            <div class="form-group">
                                <label for="started_at">When does(did) this contract start?</label>
                                <input class="form-control" type="text" name="started_at" id="started_at">
                            </div>

                            <div class="form-group">
                                <label for="ended_at">When did this contract end? (Optional)</label>
                                <input class="form-control" type="text" name="ended_at" id="ended_at">
                            </div>

                            <div class="form-group">
                                <label for="pdf">Your contract</label>
                                <input class="form-control" id="pdf" type="file" name="path">
                            </div>

                            <div class="form-group">
                                <label for="finish_by">Make a note about this contract.</label>
                                <textarea class="form-control" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <input class="btn btn-primary pull-right" id="" type="submit">
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection