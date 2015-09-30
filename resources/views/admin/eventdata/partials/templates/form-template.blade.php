@section('main')

    <div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"> {{ $modelName }} {{ $model ? $model->name : ""}}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{!! link_to_route($returnRoute, 'Back to ' . $modelName, array("q"=>$search), array('class' => 'btn btn-outline btn-warning')) !!}</span>
        </ul>
        <h4>Edit {{ $modelName }}</h4>
        <div class='col-lg-6'>
            {!! Form::model($model, $formAction) !!}

            <div class="form-group">
                {!! Form::label('icon_id', 'Icon:') !!}
                <select class="icon-select form-control" name="icon_id">
                    @foreach($icons as $icon)
                        <option value="{{ $icon->id }}" data-icon-url="{{ $icon->icon_url  }}" >{{ $icon->name }}</option>
                    @endforeach
                </select>
                <span>{!! link_to_route(Route::currentRouteName(), "More Icons", Route::current()->parameters() + array("all_icons" => true)) !!}</span>
            </div>

            <div class="form-group">
                {!! Form::label('name', 'Name:') !!}
                {!! Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('short_name', 'Short Name:') !!}
                {!! Form::text('short_name', null, array('class' => 'form-control', 'placeholder' => 'Name')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('default_name', 'Default Name:') !!}
                {!! Form::text('default_name', null, array('class' => 'form-control', 'placeholder' => 'Name')) !!}
            </div>

            @if( ! in_array( 'description', $excludedFields ) )
            <div class="form-group">
                {!! Form::label('description', 'Description:') !!}
                {!! Form::text('description', null, array('class' => 'form-control', 'placeholder' => 'Description')) !!}
            </div>
            @endif

            <div class="form-group">
                <label>Display: </label>

                <label class="radio-inline">
                    {!! Form::radio('display_flag', 0) !!} No
                </label>
                <label class="radio-inline">
                    {!! Form::radio('display_flag', 1) !!} Yes
                </label>
            </div>

            @foreach($extraFields as $header=>$field)
                <div class="form-group">
                    {!! Form::label($field['field'], $header) !!}
                    @if($field['type'] == 'icon-select')
                        <select name="{{ $field['field'] }}" class="icon-select form-control" >
                            @foreach($field['icons'] as $icon)
                                <option value="{{ $icon->id }}" data-icon-url="{{ $icon->icon_url  }}" >{{ $icon->name }}</option>
                            @endforeach
                        </select>
                    @elseif($field['type'] == 'datetime')
                        {!! Form::datetime($field['field'], object_get($model, $field['field']), array()) !!}
                    @elseif($field['type'] == 'select')
                        <select name="{{ $field['field'] }}" class="form-control select2">
                            @foreach($field['data'] as $option)
                                <option value="{{ $option->id }}" {{object_get($model, $field['field']) == $option->id ? "selected" : ""}}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    @elseif($field['type'] == 'multi-select')
                        <select name="{{ $field['field'] }}[]" class="form-control select2" multiple>
                            @foreach($field['data'] as $option)
                                <option value="{{ $option->id }}" {{ in_array($option->id, $model ? $model->$field['field']->lists('id')->all() : array()) ? "selected" : ""}}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    @else
                        {!! Form::text($field['field'], null,  array('class' => 'form-control', 'placeholder' => $header)) !!}
                    @endif
                </div>
            @endforeach

            {{--@section('market-type-group')--}}
                {{--@stop--}}
        @yield('market-type-group')
        {{--@if(isset($model)))--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::label('market_type_group_id', 'Group: ') !!}--}}
                {{--{!! Form::select('market_type_group_id', $extraFields["Market Rules"]["market_type_group_list"], $model->market_type_group_id, array('id' => 'groups', 'class' => 'form-control')) !!}--}}
            {{--</div>--}}
                {{--@else--}}
                {{--{!! Form::label('market_type_group_id', 'Group: ') !!}--}}
                {{--{!! Form::select('market_type_group_id', $extraFields["Market Rules"]["market_type_group_list"], '', array('id' => 'groups', 'class' => 'form-control')) !!}--}}
            {{--@endif--}}
        {{--</div>--}}


        <div class="col-lg-12">
            <div class="form-group">
                {!! Form::submit('Save', array('class' => 'btn btn-info')) !!}
            </div>
            {!! Form::close() !!}
        </div>
        @if ($errors->any())
        <ul>
            {{ implode('', $errors->all('<li class="error">:message</li>')) }}
        </ul>
        @endif


    </div>
    <!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
    $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
    //$('select').select2();
</script>
<!-- /.row -->
@stop