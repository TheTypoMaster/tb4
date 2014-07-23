<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			@foreach($data[0] as $key => $value)
			<th>{{ $key }}</th>
			@endforeach
		</tr>
	</thead>

	<tbody>
		@foreach($data as $row)
		<tr>
			@foreach($row as $value)
			<td>{{ $value }}</td>
			@endforeach
		</tr>
		@endforeach	
	</tbody>
</table>	