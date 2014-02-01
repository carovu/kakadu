<?php

class Course extends Eloquent {

    public $timestamps = TRUE;


    public function catalog() {
        return $this->belongsTo('Catalog', 'catalog');
    }

    public function learngroups() {
        return $this->belongsToMany('Learngroup', 'learngroup_courses');
    }

}