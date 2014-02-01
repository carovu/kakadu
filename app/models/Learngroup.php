<?php

class Learngroup extends Eloquent {

    public $timestamps = TRUE;


    public function courses() {
        return $this->belongsToMany('Course', 'learngroup_courses');
    }

    public function users() {
        return $this->belongsToMany('User', 'user_learngroups')->withPivot('role_id');
    }

}