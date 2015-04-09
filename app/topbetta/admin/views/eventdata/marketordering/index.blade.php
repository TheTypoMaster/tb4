@extends('layouts.master')

@section('main')



    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Market Ordering</h2>

                {{ Form::open(array('method' => 'GET', "class" => "form-inline")) }}
                <div class="form-group custom-search-form col-lg-4 pull-right">
                    <label for="competition">Competition: </label>
                        <select name="competition" class="form-control" id="competition-select">
                            <option value="0">Default</option>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}" {{ $competitionId == $competition->id ? "selected" : "" }}>{{ $competition->name }}</option>
                            @endforeach
                        </select>

                </div>
                {{ Form::close() }}
            </div>

            @if(count($marketTypes))
                <div class="form-group pull-right">
                    <select class="market-types select2">
                        @foreach($marketTypes as $marketType)
                            <option value="{{ $marketType->id }}">{{ $marketType->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn" id="add-market-type">Add</button>
                </div>
            @endif

            {{Form::open(array('method' => 'POST', "route" => array('admin.marketordering.store', "competition" => $competitionId))) }}
            <table class="table table-striped table-bordered table-hover" id="market-ordering-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Market Type</th>
                    <th colspan="1">Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($marketOrdering as $marketType)
                    <tr>
                        <input type="hidden" name="market-types[]" value="{{ $marketType->id }}" />
                        <td>{{ $marketType->id }}</td>
                        <td>{{ $marketType->name }}</td>
                        <td>
                            <button class="btn up-arrow"><i class="glyphicon glyphicon-arrow-up"></i></button>
                            <button class="btn down-arrow"><i class="glyphicon glyphicon-arrow-down"></i></button>
                            <button class="btn btn-danger remove-market"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <button class="btn btn-primary btn-large form-control" type="submit">Save</button>
            {{Form::close()}}

        </div>
        <!-- /.col-lg-12 -->
    </div>



    <script type="text/javascript">

        registerClickEvents();

        $("#add-market-type").click(function(e){
            e.preventDefault();

            var $select = $('.market-types');

            if($('#market-ordering-table').find('tbody').find('input[value="'+$select.val() + '"]').length === 0) {
                $("#market-ordering-table").find('tbody').append(
                        "<tr>" +
                        '<input type="hidden" name="market-types[]" value="' + $select.val() + '" />' +
                        "<td>" + $select.val() + "</td>" +
                        "<td>" + $select.find(':selected').text() + "</td>" +
                        "<td>" +
                        '<button class="btn up-arrow"><i class="glyphicon glyphicon-arrow-up"></i></button> ' +
                        '<button class="btn down-arrow"><i class="glyphicon glyphicon-arrow-down"></i></button> ' +
                        '<button class="btn btn-danger remove-market"><i class="glyphicon glyphicon-remove"></i></button> ' +
                        "</td>" +
                        "</tr>"
                );
            }

            registerClickEvents();
        });

        $('#competition-select').change(function(){
            $(this).parents('form').submit();
        })

        function registerClickEvents()
        {
            $(".up-arrow").click(function(e){
                e.preventDefault();

                $(this).parents('tr').after($(this).parents('tr').prev());
            });

            $(".down-arrow").click(function(e){
                e.preventDefault();

                $(this).parents('tr').before($(this).parents('tr').next());
            });

            $(".remove-market").click(function(e){
                e.preventDefault();

                $(this).parents('tr').remove();
            });
        }
    </script>

@stop