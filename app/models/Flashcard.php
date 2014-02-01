<?php

class Flashcard extends Eloquent {

    public $timestamps = TRUE;


    public function user() {
        return $this->belongsTo('User');
    }

    public function question() {
        return $this->belongsTo('Question');
    }

}