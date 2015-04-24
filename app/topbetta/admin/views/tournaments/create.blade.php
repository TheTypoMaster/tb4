@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Tournaments</h2>
            </div>

            {{ Form::open(array("route" => "admin.tournaments.store", "method" => "post")) }}
            <div class="col-lg-6">
                <div class="form-group">
                    {{ Form::label('sport_id', 'Sports') }}
                    {{ Form::select('sport_id', $sports, null, array("class"=>"form-control")) }}
                </div>

                <div class="form-group">
                    {{ Form::label('competition_id', 'Competitions') }}
                    {{ Form::select('competition_id', array("Select Competition"), null, array("class"=>"form-control")) }}
                </div>

                <div class="form-group">
                    {{ Form::label('event_group_id', 'Event Group') }}
                    {{ Form::select('event_group_id', array("Select Event Group"), null, array("class" => "form-control")) }}
                </div>
            </div>


        </div>
    </div>

    <script type="text/javascript">

        function createSelectOptions(json) {
            var html = $();

            $.each(json, function(index, value){
                html = html.add($('<option></option>').text(value).val(index));
            });

            return html;
        }

        $('#sport_id').change(function(){
            $.get('/admin/tournaments/get-competitions/' + $(this).val())
                .done(function(data) {
                    $('#competition_id').html(createSelectOptions(data));
                });
        });

        $('#competition_id').change(function(){
            $.get('/admin/tournaments/get-event-groups/' + $(this).val())
                .done(function(data){
                    $('#event_group_id').html(createSelectOptions(data));
                });
        })
    </script>
@stop