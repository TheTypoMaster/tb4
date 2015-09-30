<div class="row">
    <div class="col-lg-12">
        <div class="row page-header">
            <h2 class="col-lg-4">
                {{ $modelName }} <small>({{ number_format($modelCollection->total()) }})</small>
                <a href="{{route($createRoute, array("q" => $search))}}" class="btn btn-primary">Create <i class="glyphicon glyphicon-plus"></i></a>
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

        @if (count($modelCollection))
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Short Name</th>
                    <th>Default Name</th>
                    <th>Description</th>
                    <th>Display</th>
                    @foreach($extraFields as $header=>$field)
                        <th>{{ $header }}</th>
                    @endforeach
                    {{--<th>Group</th>--}}
                    <th>Created</th>
                    <th>Updated</th>
                    <th colspan="1">Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($modelCollection as $model)
                    <tr>
                        <td>{{ $model->id }}</td>
                        <td>
                            @if($model->icon)
                                <img src="{{ $model->icon->icon_url }}" style="width:25px;height:25px;">
                            @endif
                        </td>
                        <td>{{ $model->name }}</td>
                        <td>{{ $model->short_name }}</td>
                        <td>{{ $model->default_name }}</td>
                        <td>{{ $model->description }}</td>
                        <td>{{ $model->display_flag ? "Yes" : "No" }}</td>

                        @foreach($extraFields as $field)
                            <td>
                                @if($field['type'] == 'image')
                                    <img src="{{ object_get($model, $field['field']) }}" />
                                @elseif($field['type'] == 'closure')
                                    {{ call_user_func($field['field'], $model) }}
                                @else
                                    {{ object_get($model, $field['field']) }}
                                @endif
                            </td>
                        @endforeach
                        {{--<td>{{ $model->markettypegroup->market_type_group_name }}</td>--}}
                        <td>{{ $model->created_at }}</td>
                        <td>{{ $model->updated_at }}</td>
                        <td>
                            @if ( $createChildRoute )
                                {!! link_to_route($createChildRoute['route'], 'Create' . $createChildRoute['name'], array('q' => $search, $createChildRoute['param'] => $model->id), array('class' => 'btn btn-primary') ) !!}
                            @endif
                            <a href="{{ URL::route($editRoute, array($model->id, "q" => $search)) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i> Edit</a>

                            {!! Form::open(array("method" => "DELETE", "route"=>array($deleteRoute, $model->id))) !!}
                                <button type="submit" class="btn btn-danger delete-button"><i class="glyphicon glyphicon-remove"></i> Delete</button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $modelCollection->appends(array('q' => $search))->render() !!}
        @else
            <p>There is nothing to display</p>
        @endif
    </div>
</div>
