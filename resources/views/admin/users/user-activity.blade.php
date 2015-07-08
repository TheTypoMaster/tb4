@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">User Activity</h2>
            </div>


            <p>
                Required CSV Format: first name, last name, DOB <br/><small>e.g. John, Smith, 01/01/2001</small>
            </p>

            @if( ( $invalidData = Session::get('invalidData', array()) ) &&  ! empty($invalidData) )
                <h4>Couldn't Process:</h4>
                <ul>
                    @foreach($invalidData as $record)
                        <li>{{ $record }}</li>
                    @endforeach
                </ul>
            @endif

            {!! Form::open(array('url' => 'admin/user-activity/download', 'method' => "POST", "files" => true)) !!}

            <div class="form-group">
                {!! Form::label("users", "Users CSV: ") !!}
                {!! Form::file("users") !!}
            </div>

            <div class="form-group">
                {!! Form::submit("Process", array("class" => "form-control btn btn-primary")) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    @if( $file = Session::get('filename', null) )
         <script type="text/javascript">
             $(document).ready(function(){
                 location.href = "/admin/user-activity/download?filename={{ $file }}";
             });
         </script>
    @endif
@stop