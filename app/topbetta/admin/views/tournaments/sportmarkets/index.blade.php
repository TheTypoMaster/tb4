@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Sport Competitions</h2>


                <h2 class="col-lg-4 pull-right">
                    {{ Form::open(array('method' => 'GET')) }}
                    <div class="input-group custom-search-form">
                        {{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search (id,name)...")) }}
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                    {{ Form::close() }}
                </h2>
            </div>

            {{ Form::open(array('method' => 'GET', 'class' => 'form-inline')) }}
                {{ Form::label('sport', "Sport") }}
                {{ Form::select('sport', array(), null, array("class" => "form-control")) }}

                {{ Form::label('competition', "Competition") }}
                {{ Form::select('competition', array(), null, array("class" => "form-control")) }}

                {{ Form::submit('Filter', array("class" => "form-control btn btn-primary")) }}
            {{Form::close()}}

            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Sport</th>
                    <th>Start Time</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($competitions as $competition)
                    <tr>
                        <td>{{ $competition->id }}</td>
                        <td>{{ $competition->name }}</td>
                        <td>{{ $competition->sport->name }}</td>
                        <td>{{ $competition->start_date }}</td>
                        <td>
                            {{ link_to_route('admin.tournament-sport-markets.edit', "Edit", array($competition->id, "q" => $search, 'sport'=>$sport, 'competition' => $selectedComp), array("class" => "btn btn-warning")) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if ( ! is_array($competitions) )
                {{ $competitions->appends(array('q' => $search))->links() }}
            @endif
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

    <script type="text/javascript">
        //TODO: clean up and abstract
        (function(){
            function createSelectOptions(json, parameters) {
                var html = $(),
                    options = parameters || {},
                    textField = options.textField || 'name',
                    valField = options.valField || 'id',
                    selected = options.selected || 0;

                console.log(textField);
                html = html.add($('<option></option>').text('Select...').val(0));

                $.each(json, function(index, value){
                    var $option = $('<option></option>').text(value[textField]).val(value[valField]);

                    if( value[valField] == selected ) { $option.attr('selected', 'selected') }

                    html = html.add($option);
                });

                return html;
            }

            $.fn.sportCompetitionFilter = function(competitionTarget) {

                var $this = $(this);

                $.get("/admin/sports-list")
                    .done(function(data){
                        $this.html(createSelectOptions(data, {
                            'textField' : 'sport_name',
                            'valField' : 'sport_id',
                            'selected' : "{{ $sport }}"
                        }));

                        $this.change();
                    });

                $(this).change(function(e) {
                    if( $(this).val() > 0 ) {
                        $.get("/admin/sports/" + $(this).val() + "/competitions")
                                .done(function (data) {
                                    $(competitionTarget).html(createSelectOptions(data, {"selected": "{{ $selectedComp }}"}));
                                })
                    }
                });
            }

        }($));

        $('#sport').sportCompetitionFilter('#competition');

    </script>
@stop	