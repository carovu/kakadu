
{{-- Sidebar --}}
@section('sidebar')

@stop

{{-- Scripts --}}
@section('scripts')

@stop


@section('content')

	<div class="row-fluid">
		<div class="offset1">
			<div class="span12">			    
				    <legend>{{trans('import.check_import')}}</legend>
				    <p>{{trans('import.check_import2')}}</p>
				    <table class="table table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Parent</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($import['catalogs'] as $catalog){
									echo "<tr>
											<td>".$catalog['id']."</td>
											<td>".$catalog['name']."</td>
											<td>".$catalog['parent']."</td>
										 </tr>";
								}
							
							?>
						</tbody>
					</table>
					
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{trans('import.catalog')}}</th>
								<th>{{trans('import.typ')}}</th>
								<th>{{trans('import.question')}}</th>
								<th>{{trans('import.answer')}}</th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($import['questions'] as $question){
									echo "<tr>";
									echo "<td>";
									foreach($question['catalogs'] as $catalog){
										echo $catalog.", ";
									}
									echo "</td>";
									echo "<td>".$question['type']."</td>";
									echo "<td>".$question['data']['question']."</td>";
									if($question['type'] === "simple"){
										echo "<td>".$question['data']['answer']."</td>";
									}else{
										echo "<td>";
										foreach($question['data']['answer'] as $answer){
											echo $answer.", ";
										}
										echo "</td>";
										foreach($question['data']['choices'] as $choice){
											echo "<td>".$choice."</td>";
										}

									}
									echo "</tr>";
									
								}
							
							?>
						</tbody>
					</table>
					
						{{ Form::open(array('url' => 'api/v1/import/save', 'method' => 'post')) }} 					

					    {{ Form::hidden('answer', 'true') }}
					
					    {{ Form::submit(trans('import.confirm'), array('class' => 'btn btn-success')) }}
					    <a href="{{ URL::route('course/import', array($course['id'])) }}" class="btn btn-danger">{{trans('import.abort')}}</a>
					
					    {{ Form::token() }}
				    {{ Form::close() }}
			</div>
		</div>
	</div>

@stop
