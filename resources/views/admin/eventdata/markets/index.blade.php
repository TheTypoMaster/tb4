@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Markets
                    <small>({{ count($markets) ? number_format($markets->total()) : 0 }})</small>
                </h2>
                <h2 class="col-lg-4 pull-right">
                    {!! Form::open(array('method' => 'GET')) !!}
                    <div class="input-group custom-search-form">
                        {!! Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) !!}
                        <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                    </div>
                    {!! Form::close() !!}
                </h2>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="pull-right">
                <div class="pull-right">
                    {!! link_to_route('admin.events.index', "Back to Events", array(), array("class" => "btn btn-outline btn-warning")) !!}
                </div>
            </div>

            @if (count($markets))
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Market Name</th>
                        <th>Event Name</th>
                        <th>Market Status</th>
                        <th>Display</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th colspan="1">Action</th>
                    </tr>
                    </thead>

        	<tbody>
        		@foreach($markets as $market)
        		<tr>
        			<td>{{ $market->id }}</td>
        			<td>{{ $market->market_type_name . ($market->line ? ' (+/-' . $market->line . ')' : ''); }}</td>
        			<td>{{ $market->event_name }}</td>
                    <td>{{ $market->market_status }}</td>
        			<td>{{ ($market->display_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $market->created_at }}</td>
        			<td>{{ $market->updated_at }}</td>
        			<td>
                        {!! link_to_route('admin.markets.edit', 'Edit', array($market->id, "q" => $search), array('class' => 'btn btn-info')) !!}
                        {!! link_to_route('admin.selections.index', 'Selections', array('market' => $market->id, 'event' => $event), array('class' => 'btn btn-primary')) !!}
                    </td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {!! $markets->appends(array('q' => $search, 'event' => $event))->render() !!}
        @else
        <p>There are no markets to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop