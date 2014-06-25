<!DOCTYPE html>
<html>

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>TopBetta Admin</title>

		<!-- Core CSS - Include with every page -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

		<!-- Page-Level Plugin CSS - Blank -->

		<!-- SB Admin CSS - Include with every page -->
		<link href="/css/sb-admin.css" rel="stylesheet">

	</head>

	<body>
		<div class="container">
			<div class="row">
				<div>
					@if (Session::has('flash_message'))
					<div class="alert alert-info alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" title="Close Message" aria-hidden="true">&times;</button>
						<p>{{ Session::get('flash_message') }}</p>
					</div>     
					@endif
				</div>
				@yield('main')            

			</div>
		</div>

		<!-- Core Scripts - Include with every page -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>

		<script>
			$(function() {
				$('.alert-dismissable').fadeOut(5000);
			});
		</script>

	</body>

</html>
