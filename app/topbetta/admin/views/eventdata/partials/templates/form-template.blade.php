@section('main')
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"> {{ $modelName }} {{ $model->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route($returnRoute, 'Back to Sports', array("q"=>$search), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
        <h4>Edit {{ $modelName }}</h4>
        <div class='col-lg-6'>
            {{ Form::model($model, array('method' => 'PATCH', 'route' => array($updateRoute, $model->id, "q" => $search))) }}
            @if(count($icons))
            <div class="form-group">
                {{ Form::label('icon_id', 'Icon:') }}
                <select class="icon-select form-control">
                    @foreach($icons as $icon)
                        <option value="{{ $icon->id }}" data-icon-url="{{ $icon->icon_url  }}" >{{ $icon->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group">
                {{ Form::label('name', 'Name:') }}
                {{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
            </div>
            <div class="form-group">
                {{ Form::label('short_name', 'Short Name:') }}
                {{ Form::text('short_name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
            </div>
            <div class="form-group">
                {{ Form::label('default_name', 'Default Name:') }}
                {{ Form::text('default_name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
            </div>
            <div class="form-group">
                {{ Form::label('description', 'Description:') }}
                {{ Form::text('description', null, array('class' => 'form-control', 'placeholder' => 'Description')) }}
            </div>
            <div class="form-group">
                <label>Display: </label>

                <label class="radio-inline">
                    {{Form::radio('display_flag', 0)}} No
                </label>
                <label class="radio-inline">
                    {{Form::radio('display_flag', 1)}} Yes
                </label>
            </div>

            @foreach($extraFields as $header=>$field)
                {{ Form::label($field['field'], $header) }}
                @if($field['type'] == 'icon-select')
                    <select name="{{ $field['field'] }}" class="icon-select" >
                        @foreach($field['icons'] as $icon)
                            <option value="{{ $icon->id }}" data-icon-url="{{ $icon->icon_url  }}" >{{ $icon->name }}</option>
                        @endforeach
                    </select>
                @endif
            @endforeach
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                {{ Form::submit('Save', array('class' => 'btn btn-info')) }}
            </div>
            {{ Form::close() }}
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

</script>
<!-- /.row -->
@stop