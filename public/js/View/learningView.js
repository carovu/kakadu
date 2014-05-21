/**
 * Backbone View of the test
 */
QuizView = Backbone.View.extend({


	/**
	 * Initializes the view with all data.
	 * 
	 * @param: course: the it of the course which the user is learning
	 * @param: url: the base url
	 * @param: section: course|catalog|favorite
	 * @param: question: the first question
	 * @param: catalog: the id of the catalog which the user is learning
	 */
	initialize: function(course, url, section, question, catalog) {
		this.section = section;
		this.courseId = course;
		this.baseUrl = url;
		this._token = $('input[name="_token"]').val();
		this.right = 0;
		this.catalogId = catalog;
		this.answerBoolean = true;
		this.type = question.type;
		this.multipleAnswers = new Array();

		//writing the question and reading the answer
		if(this.type === "simple" || this.type === "UndefType"){
			this.simple = new simpleQuestion(question);
			$("#multiple").hide();
			$("#cloze").hide();
			$("#dragdrop").hide();
			$("#questionSimple").html(this.simple.get("question"));
			$("#answerSimple").html("");
		}else if(this.type === "multiple"){
			this.multiple = new multipleQuestion(question);
			$("#nextMultipleQuestion").hide();
			$("#simple").hide();
			$("#cloze").hide();
			$("#dragdrop").hide();
			$("#questionMultiple").html(this.multiple.get("question"));
			this.setAnswers(this.multiple.get("choices"));
		}else if(this.type ==="cloze"){
			this.cloze = new clozeQuestion(question);
			$("#multiple").hide();
			$("#simple").hide();
			$("#dragdrop").hide();
			this.setCloze(this.cloze.get("question"), this.cloze.get("answer"));
			this.showClozeAnswer(this.cloze.get("answer"));
		}else if(this.type ==="dragdrop"){
			this.dragdrop = new dragdropQuestion(question);
			$("#multiple").hide();
			$("#simple").hide();
			$("#cloze").hide();
			$("#nextDragDropQuestion").hide();
			$("#questionDragDrop").html(this.dragdrop.get("question"));
			this.setDragDrop(this.dragdrop.get("answer"), this.dragdrop.get("choices"));
		}

		this.percent = 0;
		this.number = 0;
		_.bindAll(this);
		$(document).bind('keyup', this.logKey);
		$(document).bind('keydown', this.clozeKey);
		this.state = "question";
		this.render();
	},

	el: 'body',
	render: function() {
		$(document).ready(function() {
			$("#correct").hide();
			return this;
		});
	},

	/**
	 * Assigns which function is called on which button press
	 */
	events: {
		"click button[id=showAnswer]": "showAnswer",
		"click button[id=yes]": "correct",
		"click button[id=no]": "notCorrect",
		"click button[id=checkAnswer]": "checkAnswer",
		"click button[id=nextMultipleQuestion]": "nextMultipleQuestion",
		"click button[class='btn row-fluid answer']": "checkClick",
		"click button[id=checkCloze]": "checkCloze",
		"click button[id=nextClozeQuestion]": "nextClozeQuestion",
		"click button[id=nextDragDropQuestion]": "nextDragDropQuestion",
	},

	/**
	 * Method which handles the keyboard control of the quiz
	 */
	logKey: function(e) {
		if(this.type === "simple"){
			if(this.state === "question"){
				if(e.keyCode == 13){
					this.showAnswer();
				}
			}else {
				if(e.keyCode == 49){
					this.notCorrect();
				}
				if(e.keyCode == 50){
					this.correct();
				}
			}
		}
		if(this.type === "multiple"){
			if(this.state === "question"){
				if(e.keyCode === 49){
					var pressed = this.multiple.get("choices")[0];
					pressed = escape(pressed);
					this.markAnswer(pressed);
				}
				if(e.keyCode === 50){
					var pressed = this.multiple.get("choices")[1];
					pressed = escape(pressed);
					this.markAnswer(pressed);
				}
				if(e.keyCode === 51){
					var choices = this.multiple.get("choices");
					if(choices.length >= 3){
						var pressed = this.multiple.get("choices")[2];
						pressed = escape(pressed);
						this.markAnswer(pressed);
					}					
				}
				if(e.keyCode === 52){
					var choices = this.multiple.get("choices");
					if(choices.length >= 4){
						var pressed = this.multiple.get("choices")[3];
						pressed = escape(pressed);
						this.markAnswer(pressed);
					}
				}
				if(e.keyCode == 16){
					this.checkAnswer();
				}
			}
			if(this.state === "answer"){
				if(e.keyCode == 13){
					this.nextMultipleQuestion();
				}
				
			}
		}


		if(this.type === "dragdrop"){
			if(this.state === "answer"){
				if(e.keyCode == 13){
					this.nextDragDropQuestion();
				}
			}
		}
	},
	clozeKey: function(e){
		if(this.type === "cloze"){
			if(this.state === "question"){
				if(e.keyCode == 13 && !e.shiftKey){
					e.preventDefault();
					this.checkCloze();
				}
			}else {
				if(e.keyCode == 13){
					this.nextClozeQuestion();
				}
				
			}
		}
	},
	/**
	 * Loads the next question from the server
	 */
	nextQuestion: function(){
		
		//store question ID
		if(this.type === "simple" || this.type === "UndefType"){
			this.questionId = this.simple.get("id");
		}else if(this.type === "multiple"){
			this.questionId = this.multiple.get("id");
		}else if(this.type === "cloze"){ 
			this.questionId = this.cloze.get("id");
		}else if(this.type === "dragdrop"){ 
			this.questionId = this.dragdrop.get("id");
		}
		
		$this = this;
		$.post(this.baseUrl+"/learning/next", {_token: this._token, question: this.questionId, catalog: this.catalogId, course: this.courseId, answer: this.answerBoolean, section: this.section}
		, function(data) {
			if(data.status==="Ok"){
				$this.setData(data);
			}
		}); 
		

	},

	/**
	 * Sets all relevant data of the new question. Is called on a successfull post-method call.
	 * 
	 * @param data: all data recieved from the post method
	 */
	setData: function(data){
		this.courseId = data.course;
		this.catalogId = data.catalog;
		this.type = data.type;
		this.state = "question";
		this.questionId = data.id;
		if(this.type === "simple" || this.type === "UndefType"){
			this.simple = new simpleQuestion(data);
			$("#multiple").hide();
			$("#cloze").hide();
			$("#dragdrop").hide();
			$("#simple").show();
			$("#correct").hide();
			$("#questionSimple").html(this.simple.get("question"));
			$("#answerSimple").html("");
			$("#showAnswer").show();
		}else if(this.type === "multiple"){
			this.multiple = new multipleQuestion(data);
			$("#simple").hide();
			$("#cloze").hide();
			$("#dragdrop").hide();
			$("#nextMultipleQuestion").hide();
			$("#multiple").show();
			$("#questionMultiple").html(this.multiple.get("question"))
			this.setAnswers(this.multiple.get("choices"));
		}else if(this.type === "cloze"){
			this.cloze = new clozeQuestion(data);
			$("#multiple").hide();
			$("#simple").hide();
			$("#dragdrop").hide();
			$("#cloze").show();
			$("#nextClozeQuestion").hide();
			this.setCloze(this.cloze.get("question"), this.cloze.get("answer"));
			this.showClozeAnswer(this.cloze.get("answer"));
		}else if(this.type === "dragdrop"){
			this.dragdrop = new dragdropQuestion(data);
			$("#multiple").hide();
			$("#simple").hide();
			$("#cloze").hide();
			$("#dragdrop").show();
			$("#nextDragDropQuestion").hide();
			$("#questionDragDrop").html(this.dragdrop.get("question"));
			this.setDragDrop(this.dragdrop.get("answer"), this.dragdrop.get("choices"));
		}
	},

	/**
	 * Shows the answer and the fields if the users knows the answer or not.
	 * Only needed for simple question type
	 */
	showAnswer: function(){
		$("#answerSimple").html(this.simple.get("answer"));
		$("#showAnswer").hide();
		$("#correct").show();
		this.state = "answer";
	},

	/**
	 * Is called when the user knows the answer. 
	 * Calculates the percent of right questions and calls the nextQuestion function 
	 */
	correct: function(){		
		if(this.state !== "working"){
			this.state = "working";		
			if(this.type !== "simple"){
				$("#correct").hide();
			}	
			this.right++;
			this.number++;
			this.answerBoolean = true;
			this.percent = Math.round((100/this.number)*this.right);
			$(".percent").text(this.right + "/" + this.number + " - " + this.percent + "%");
			this.nextQuestion();
		}
		
	},

	/**
	 * Is called when the user doesn't know the answer. 
	 * Calculates the percent of right questions and calls the nextQuestion function 
	 */
	notCorrect: function(){
		if(this.state !== "working"){		
			this.state = "working";
			if(this.type !== "simple"){
				$("#correct").hide();
			}		
			this.number++;
			this.answerBoolean = false;
			this.percent = Math.round((100/this.number)*this.right);
			$(".percent").text(this.right + "/" + this.number + " - " + this.percent + "%");
			this.nextQuestion();
		}
	},
	
	/**
	 * Set all answers for a multiple choice question.
	 * 
	 * @param choices: all choices
	 */
	setAnswers: function(choices){
		this.state = "question";
		$("#checkAnswer").show();
		$("#choicesLeft").empty();
		$("#choicesRight").empty();
		this.multipleAnswers = new Array();
		for(var i = 0; i < choices.length; i++){
			var answer = choices[i];
			var button = '<br><button class="btn row-fluid answer" style="border-style:solid; border-width:thick;" value="'+answer+'"><p><br>'+answer+'<br></p></button><br>';
			if(i%2 === 0){
				$("#choicesLeft").append(button);
			}else{
				$("#choicesRight").append(button);
			}
		}
		this.render();
	},

	/**
	 * Set all gaps for cloze question
	 * 
	 * @param choices: content of cloze
	 * @param choices: all answers
	 */
	setCloze: function(questionContent, answers){
		this.state = "question";
		$("#checkCloze").show();
		$("#nextClozeQuestion").hide();
		for(var i = 0; i < answers.length; i++){
			var startPos = questionContent.search(answers[i]);
			var endPos = startPos + answers[i].length;
			var before = questionContent.substr(0, startPos);
			var after = questionContent.substr(endPos, questionContent.length);
			var gap = '<textarea id="gapsAnswer['+i+']" class="span2" rows="1" style="resize:none"></textarea>';
			questionContent = before + gap + after;
			//console.log("i: "+ i + " startPos: " + startPos + " endPos: " + endPos);
			//console.log(questionContent);
		}

		$("#questionCloze").html(questionContent);
	},

	/**
	 * Set all answers for gap in cloze question
	 * 
	 * @param choices: all answers
	 */
	showClozeAnswer: function(answers){
		this.state = "question";
		var tmp = [];
		var output = "";
		for(var i = 0; i < answers.length; i++){
			tmp[i] = answers[i];
		}
		this.shuffle(tmp);
		for(var i = 0; i < tmp.length; i++){
			if(i == tmp.length-1){
				output += tmp[i];
			} else {
				output += tmp[i]+' - ';
			}
			
		}		
		$("#answerCloze").html(output);
	},

	/**
	 * Randomize array element order in-place.
	 * Using Fisher-Yates shuffle algorithm.
	 */
	shuffle: function(array){
	    for (var i = array.length - 1; i > 0; i--) {
	        var j = Math.floor(Math.random() * (i + 1));
	        var temp = array[i];
	        array[i] = array[j];
	        array[j] = temp;
	    }
	    return array;
	},
	
	/**
	 * Check if the answer of the multiple choice question
	 * was right.
	 * 
	 * @param the event which was released. It contains the button which was pressed.
	 */
	checkAnswer: function(){
		$("#checkAnswer").hide();
		$("#nextMultipleQuestion").show();
		
		$rightAnswers = this.multiple.getRightAnswers();
		$choices = this.multiple.get("choices");
		
		//save all right ansers in $answers
		$answers = new Array();
		for(var i = 0; i < $rightAnswers.length; i++){
			$answers.push(this.multiple.get("choices")[$rightAnswers[i]]);
		}
		if(this.state !== "working"){
			
			//mark right and false answers			
			for(var i = 0; i < $choices.length; i++){
				//all not selected questions which area wrong
				if($.inArray($choices[i], $answers) === -1 && $.inArray($choices[i], this.multipleAnswers) === -1){
					$("button[value='"+$choices[i]+"']").attr("disabled", "disabled");
				}
				//all not selected questions which are true
				if($.inArray($choices[i], $answers) !== -1 && $.inArray($choices[i], this.multipleAnswers) === -1){
					$("button[value='"+$choices[i]+"']").attr("style", "border-style:solid; border-width:thick; border-color:green;");
					$("button[value='"+$choices[i]+"']").attr("disabled", "disabled");
				}
			}
			for(var i = 0; i < this.multipleAnswers.length; i++){
				//all selected questions which area wrong
				if($.inArray(this.multipleAnswers[i], $answers) !== -1){;
					$("button[value='"+this.multipleAnswers[i]+"']").attr("class", "btn btn-success row-fluid answer");
				}else{
					$("button[value='"+this.multipleAnswers[i]+"']").attr("class", "btn btn-danger row-fluid answer");
				}
			}
			
			//check if all answers are correct or not
			if($(this.multipleAnswers).not($answers).length == 0 && $($answers).not(this.multipleAnswers).length == 0){
				this.answerBoolean = true;
			}else{
				this.answerBoolean = false;
			}
			this.state = "answer";
		}
		
		
	},


	/**
	 * Check if the answer(s) of the cloze question
	 * was right.
	 * 
	 * @param the event which was released. It contains the button which was pressed.
	 */
	checkCloze: function(){
		$("#checkCloze").hide();
		$("#nextClozeQuestion").show();
		var answers = this.cloze.get("answer");
		var numRightGaps = 0;
			for(var i = 0; i < answers.length; i++){
				if(document.getElementById("gapsAnswer["+i+"]").value.toLowerCase() == answers[i].toLowerCase()){
					numRightGaps++;
					document.getElementById("gapsAnswer["+i+"]").style.backgroundColor = "#9acd32";
				}else{
					document.getElementById("gapsAnswer["+i+"]").style.backgroundColor = "#FF6347";
				}
			}
			if(numRightGaps == answers.length){
				this.answerBoolean = true;
			}else{
				this.answerBoolean = false;
			}
			this.state = "answer";

	},

	checkClick: function(event){
		var pressed = $(event.currentTarget).val();
		this.markAnswer(pressed);
	},
	
	/**
	 * Set all answers for Drag&Drop question
	 * 
	 * @param choices: all choices
	 */
	setDragDrop: function(answer, choices){
		this.state = "question";
		$('#choicesDragDrop').html('');
  		$('#answerDragDrop').html('');
		//shuffle choices
		choices.sort( function() { return Math.random() - .5 } );
		//create draggable choices
		for ( var i=0; i<choices.length; i++ ) {
		    $('<div>' + choices[i] + '</div>').data( 'content', choices[i] ).attr( 'id', 'choice'+i ).appendTo( '#choicesDragDrop' ).draggable( {
		      containment: '#dragdrop',
		      stack: '#choicesDragDrop div',
		      cursor: 'move',
		      revert: true
		    } );
		  }
		//create answerfield
		$('<div>' +"Drop answer here"+ '</div>').data('content', answer).appendTo( '#answerDragDrop' ).droppable( {
	      accept: '#choicesDragDrop div',
	      hoverClass: 'hovered',
	      drop: function(event, ui){
					$("#nextDragDropQuestion").show();
					var answerContent = $(this).data('content');
			  		var choiceContent = ui.draggable.data('content');

			  		if(choiceContent == answerContent){
					    ui.draggable.addClass('correct');
					    //ui.draggable.draggable( 'disable' );
					    //$(this).droppable( 'disable' );
					    ui.draggable.position( { of: $(this), my: 'left top', at: 'left top' } );
					    ui.draggable.draggable( 'option', 'revert', false );
					    this.answerBoolean = true;
			  		} else{
			  			ui.draggable.addClass('false');
			  			ui.draggable.position( { of: $(this), my: 'left top', at: 'left top' } );
			  			ui.draggable.draggable( 'option', 'revert', false );
			  			//search for correct choice and change to green color
			  			for ( var i=0; i<choices.length; i++ ) {
			  				if ($( '#choice'+i ).data('content') == answer){
			  					$( '#choice'+i ).addClass('correct');	
			  				}
			  		 	}
			  			this.answerBoolean = false;
			  			
			  		}
			  		for ( var i=0; i<choices.length; i++ ) {
			  			$( '#choice'+i ).draggable( 'option', 'disabled', true );
			  		 }
					this.state = "answer";
				}
	    } );
	},

	/**
	 * Adds/removes the pressed button with a orange border and writes/removes it in/from an array.
	 * 
	 * @param pressed: the value of the button which was pressed
	 */
	markAnswer: function(pressed){
		console.log(pressed);
		if($.inArray(pressed, this.multipleAnswers) !== -1){
			this.multipleAnswers.splice($.inArray(pressed, this.multipleAnswers), 1);
			$("button[value='"+pressed+"']").attr("style", "border-style:solid; border-width:thick;");
		}else{
			$("button[value='"+pressed+"']").attr("style", "border-style:solid; border-width:thick; border-color:orange;");
			this.multipleAnswers.push(unescape(pressed));
		}
	},
	
	/**
	 * Loads the next multiple choice Question
	 */
	nextMultipleQuestion: function(){
		$("#nextMultipleQuestion").hide();
		if(this.answerBoolean){
			this.correct();
		}else{
			this.notCorrect();
		}
	},

	/**
	 * Loads the next cloze Question
	 */
	nextClozeQuestion: function(){
		$("#nextClozeQuestion").hide();
		if(this.answerBoolean == true){
			this.correct();
		}else{
			this.notCorrect();
		}
	},

	/**
	 * Loads the next Drag&Drop Question
	 */
	nextDragDropQuestion: function(){
		$("#nextDragDropQuestion").hide();
		if(this.answerBoolean == true){
			this.correct();
		}else{
			this.notCorrect();
		}
	}

});