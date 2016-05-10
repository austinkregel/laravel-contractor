@extends('spark::layouts.app')
@section('styles.top')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" rel="stylesheet ">
@endsection
@section('content')
    <div class="container">
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
                              action="{{ route('contractor::post', ($ticket->id)) }}">
                            <div class="form-group">
                                <input class="form-control" id="_redirect" type="hidden" name="_redirect"
                                       value="{{ route('contractor::home') }}">
                            </div>
                            {!! method_field('put') !!}
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="name">Name the contract</label>
                                <input class="form-control" type="text" name="name" value="{{ $ticket->name }}">
                            </div>

                            <div class="form-group">
                                <label for="who_its_through">Who is this contract with? (The other party
                                    involved)</label>
                                <input class="form-control" type="text" name="who_its_through"
                                       value="{{ $ticket->who_its_through }}">
                            </div>

                            <div class="form-group">
                                <label for="started_at">When does(did) this contract start?</label>
                                <input class="form-control" type="text" name="started_at" id="started_at" value="{{ $ticket->started_at->format('Y-m-d') }}">
                            </div>

                            <div class="form-group">
                                <label for="ended_at">When did this contract end? (Optional)</label>
                                <input class="form-control" type="text" name="ended_at" id="ended_at" value="{{ $ticket->ended_at->format('Y-m-d') }}">
                            </div>

                            <div class="form-group">
                                <label for="finish_by">Make a note about this contract.</label>
                                <textarea class="form-control" name="description">{{ $ticket->description }}</textarea>
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
        <div class="col-md-12">
            <form id="createFormModels" class="dropzone" action="{{ route('contractor::api.v1.new-pdf', $ticket->id ) }}" method="POST">
                {!! csrf_field() !!}
            </form>
        </div>
        <div class="row">
            <div class="col-md-12" id="associations">
                <h2 class="col-md-12">Associated PDFs</h2>
                @foreach($ticket->paths as $path)
                    <div class="col-md-4 col-sm-6">
                        <div class="panel panel-default contract">
                            <div class="panel-heading">
                                {{ $path->uuid }}
                                <span class="pull-right" style="position:absolute;right:2.3rem;">
                                    {{--<i class="material-icons">edit</i>--}}
                                    <form id="{{ $path->uuid }}" action="{{route('warden::api.delete-model', ['path',$path->id])}}" method='post' class="contract" @submit.prevent="makeRequest">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                                        <button type="submit" class="method-button"><i class="material-icons red-text">delete</i></button>
                                    </form>
                                </span>
                            </div>
                            <div class="panel-body">
                                <div>Uploaded at: {{ $path->created_at->format('D M j G:i:s') }}</div>
                                <a href="{{ route('contractor::api.v1.get-document', $path->uuid) }}" class="btn">View this pdf</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        new Vue({
            el: "#associations",
            data: {
                response: '',
                debug: '',
                data: {
                    _token: "{{csrf_token()}}",
                }
            },
            methods: {
                makeRequest: function (e) {
                    e.preventDefault();
                    var responseArea = document.getElementById('response');
                    request(e.target.action, 'delete', this.$data.data,
                            function(responseArea){
                                $(e.target).parent().parent().parent().parent().remove();

                                Materialize.toast($('<span>'+responseArea.message+'!<i class="material-icons green-text">check</i></span>'), 5000);
                            }, function(responseArea){
                                Materialize.toast($('<span>'+responseArea.message+'!<i class="material-icons amber-text">warning</i></span>'), 5000);
                            }, function(responseArea){
                                Materialize.toast($('<span>'+responseArea.message+'!<i class="material-icons red-text">error</i></span>'), 5000);
                            });
                },
                close: close,
                updateSelect: function () {
                    // Leave this here so we don't get view errors
                }
            }
        })
        @include('formmodel::request', ['type' => 'delete'])
    </script>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <script>
        Dropzone.options.createFormModels = {
            paramName: 'path',
            maxFilesize: 32,
            acceptedFiles:'.pdf'
        }
    </script>
@endsection