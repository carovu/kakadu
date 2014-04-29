
{{-- Scripts --}}
@section('scripts')

{{ HTML::script('js/jquery-ui-1.10.0.js')}}
{{ HTML::script('js/Model/simpleQuestion.js')}}
{{ HTML::script('js/Model/multipleQuestion.js')}}
{{ HTML::script('js/Model/clozeQuestion.js')}}
{{ HTML::script('js/Model/dragdropQuestion.js')}}
{{ HTML::script('js/View/learningView.js')}}
{{HTML::style('css/dragdrop.css')}}

<script>

$(document).ready(function(){
	
	//Create View
	var question = <?php echo json_encode($question); ?>;
	view = new QuizView('{{$course["id"]}}', '{{URL::to('/api/v1')}}', '{{$section}}', question, '{{$catalog["id"]}}');

	//Hides the alerts when clicking close
	$('.alert .close').live('click',function(){
		$(this).parent().hide();
		return false;
	});
	
});
	

</script>


@stop


{{-- Content --}}
@section('content')

    
<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			
			<legend><h1>Quiz</h1></legend>
			<h3><span id="name"></span></h3>
			
			<!-- Include of the different question types. Depending on the question type only one type is visible.  -->
			
			<!-- Cloze question type -->
			@include('learning.types.cloze')

			<!-- Drag&Drop question type -->
			@include('learning.types.dragdrop')
			
			<!-- Simple Question type -->
			@include('learning.types.simple')
			
			<!-- Multiple choice question type -->
			@include('learning.types.multiple')
			
			<p>{{trans('general.back_course')}}{{Html::linkRoute('course', $course['name'], array($course['id']))}}</p>
			<p>{{trans('general.back_catalog')}}{{Html::linkRoute('catalog', $catalog['name'], array($catalog['id']))}}</p>
		</div>
	</div>
</div>

			

@stop