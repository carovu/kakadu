
var courseId;
var choices;
var _token;
var url;
var takenGaps;
var wholeClozeText;
var numGaps;
var type;
var preview;
/**
 * Initialises the file with the course id and hides the
 * multiplechoice question type. 
 * @param id: the id of the course
 * @param baseUrl
 */
function initialiseQuestionType(id, baseUrl, questionType){
	this.courseId = id;
	this.choices = 2;
	this.url = baseUrl;
	this.numGaps = 0;
	this.takenGaps = [];
	this.wholeClozeText = "";
	this.type = questionType;
	this.preview ="";
	console.log("questionType:" + this.type);
	this._token =  $('input[name="_token"]').val();	
	if(this.type === "simple"){
		$("#multiple").hide()
		$("#cloze").hide()
		$("#simple").show();
	}else if(this.type === "multiple"){
		$("#simple").hide();
		$("#cloze").hide()
		$("#multiple").show();
	}else {
		$("#simple").hide();
		$("#multiple").hide()
		$("#cloze").show();
	}
}

/**
 * Shows the given question type and hides the other one(s).
 * 
 * @param type: the type of the question
 */
function changeType(type){
	if(type === 'simple'){
		$("#simple").show();
		$('#multiple').hide();
		$("#cloze").hide()
	}
	if(type === 'multiple'){
		$('#multiple').show();
		$("#simple").hide();
		$("#cloze").hide()
	}
	if(type === 'cloze'){
		$('#cloze').show();
		$("#simple").hide();
		$("#multiple").hide()
	}
}

/**
 * Reads all changed values and edits the question.
 * 
 * @param id: id of the question.
 * @param type: type of the question
 */
function editQuestion(id, type){
	var questionId = id;
	var questionType = type;
	
	console.log(questionId, questionType)
	
	var question = $('#editQuestion'+questionId+' #questionSimple').val();
	var questionJson = JSON.stringify({"question": question});
	console.log(questionJson);
	
	var answer = $('#editQuestion'+questionId+' #answerSimple').val();
	var answerJson = JSON.stringify({"answer": answer});
	console.log(answerJson);
	
	var catalogsSelected = $('#editQuestion'+questionId+' #selectCatalogs').val() || [];
	console.log(catalogsSelected);
}

/**
 * Adds an extra answer choice
 */
function addChoice(){
	var numChoices = $('.choices').length
	
	var extraChoice = '<div id="'+numChoices+'">'+
					  '<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none"></textarea>'+
				 	  '<input id="check'+numChoices+'" name="right" class="offset1" type="checkbox" value="'+numChoices+'" name="checkbox">'+
				 	  '<button class="btn-danger offset1" onclick="removeChoice('+numChoices+');return false;"><i class="icon-remove"></i></button>'+
				 	  '</div>';
	
	$("#formMultiple #choices").append(extraChoice);
}

/**
 * Adds another gap for cloze and creates a preview of quiz view
 */
function addGap(){
    $('#clozequestion').focus();
    wholeClozeText = $('#clozequestion').val();
	var before = "";
	var after = "";
	var space = 0;
	
	//if nothing is selected
	if($('#clozequestion').selection() == ""){
		window.alert("You have not selected any text");
		return false;
	//if something is selected
	} else if($('#clozequestion').selection() != null){
		if(numGaps == 0){
			takenGaps = [];
			takenGaps.push($('#clozequestion').selection());
			takenGaps.push($('#clozequestion').selection('getPos').start);
			takenGaps.push($('#clozequestion').selection('getPos').end);
			before = $('#clozequestion').val().substr(0, $('#clozequestion').selection('getPos').start);
			after = $('#clozequestion').val().substr($('#clozequestion').selection('getPos').end, $('#clozequestion').val().length);
		} else {
			//if gaps are overlapping
			//array saves selected Text, startPos, selected Text, endPos,startPos, endPos, ...
			for(var i = 0; i < takenGaps.length; i+=3){
				if((($('#clozequestion').selection() == takenGaps[i]) && ($('#clozequestion').selection('getPos').start == takenGaps[i+1]) && ($('#clozequestion').selection('getPos').end == takenGaps[i+2]))
					|| (($('#clozequestion').selection('getPos').end >= takenGaps[i+1]) && ($('#clozequestion').selection('getPos').end <= takenGaps[i+2]))
					|| (($('#clozequestion').selection('getPos').start >= takenGaps[i+1]) && ($('#clozequestion').selection('getPos').start <= takenGaps[i+2])))
				{
					window.alert("Gap is overlapping with another gap");
					return false;
				}
			}
			//if the same word occurs more than once
			if(jQuery.inArray($('#clozequestion').selection(), takenGaps) != -1)
			{
				takenGaps.push($('#clozequestion').selection());
				takenGaps.push($('#clozequestion').selection('getPos').start);
				takenGaps.push($('#clozequestion').selection('getPos').end);
				startPos = preview.search($('#clozequestion').selection()+" ");
				endPos = startPos + $('#clozequestion').selection().length;
				before = preview.substr(0, startPos);
				after = preview.substr(endPos, preview.length);
			//if highlighted like inteded
			} else {
				takenGaps.push($('#clozequestion').selection());
				takenGaps.push($('#clozequestion').selection('getPos').start);
				takenGaps.push($('#clozequestion').selection('getPos').end);
				startPos = preview.search($('#clozequestion').selection());
				endPos = startPos + $('#clozequestion').selection().length;
				before = preview.substr(0, startPos);
				after = preview.substr(endPos, preview.length);
			}
		}
		var gap = '<button class="btn-danger" id="'+numGaps+'" onclick="removeGap('+numGaps+');return false;"><i class="icon-remove"></i></button>'+
				  '<input type="hidden" id="'+numGaps+'" name="answer[]" value='+$('#clozequestion').selection()+'>';
		preview = before + gap + after;
		numGaps++;
		console.log(takenGaps);
		console.log(preview);
		$("#formCloze #preview").html(preview);
	}
}

/**
 * Removes an extra gap and replaces created gap in preview with the word again.
 * 
 * @param id: the id of the extra gap which is removed
 */
function removeGap(id){
	$('input[type="hidden"][id="'+id+'"][name="answer[]"]').remove();
	$("#"+id).replaceWith(takenGaps[id*3]);
	numGaps--;
	takenGaps[id*3] = null;
	takenGaps[(id*3)+1] = null;
	takenGaps[(id*3)+2] = null;
	console.log(takenGaps);
	$("#formCloze #preview").html();
	

}


/**
 * Removes an extra choice.
 * 
 * @param id: the id of the extra choice which is removed
 */
function removeChoice(id){
	if($("#check"+id).is(':checked')){
		console.log("dsfadsafsad");
		console.log(id);
		$('input[type="hidden"][value="'+id+'"][name="answer[]"]').remove();
	} 
	$("#"+id).remove();
}

/**
 * Is fired on a click on a radio button. It reads the value of the radio button
 * and writes it in the hidden question field.
 */
$(document).ready(function(){
	$('#choices').on('change', 'input:checkbox', function () {
		if($(this).attr('checked')){	
			var answer = '<input type="hidden" name="answer[]" value='+$(this).val()+'>';
			$("#formMultiple #choices").append(answer);
		}else{
			$('input[type="hidden"][value="'+$(this).val()+'"]').remove();
		}
	});
});



