<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TopBetta Admin</title>

    <!-- Core CSS - Include with every page -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css" rel="stylesheet">


    <!-- Page-Level Plugin CSS - Blank -->

    <!-- SB Admin CSS - Include with every page -->
    <link href="/css/sb-admin.css" rel="stylesheet">

</head>

<body>

    <!-- Core Scripts - Include with every page -->
    <script src="/js/jquery-2.1.1.min.js"></script>
    <script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/js/bootstrap-datetimepicker.min.js"></script>

    <div id="wrapper">

		{{--  navbar has sidebar nested --}}
		@include('admin.layouts.partials.navbar')

        <div id="page-wrapper">
			@if (Session::has('flash_message'))
			<div class="alert alert-info alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" title="Close Message" aria-hidden="true">&times;</button>
				<p>{{ Session::get('flash_message') }}</p>
			</div>     
			@endif
			@yield('main')            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- SB Admin Scripts - Include with every page -->
    <script src="/js/sb-admin.js"></script>

</body>

</html>
