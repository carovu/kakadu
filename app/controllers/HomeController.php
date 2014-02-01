<?php

class HomeController extends BaseController {


    /**
     * Display the home screen.
     * 
     * Depending if the user is logged in or not the screen shows a login field or the user informations.
     */
    public function getIndex() {
        $view = View::make('home.index');

        //Get informations of logged in user
        if($this->user !== null) {
            $userSentry = Sentry::getUser();

            //Get the learngroups of the user
            $groups = HelperGroup::getLearngroupsOfUser($userSentry);
            $view->learngroups = $groups;

            //Get favorites of the user
            $favorites = HelperFavorite::getFavorites($userSentry);
            $view->courses = $favorites['courses'];
            $view->catalogs = $favorites['catalogs'];
        }
        $this->layout->content = $view;
    }


    public function getHelp() {
        $view = View::make('home.help');
        $this->layout->content = $view;
    }

}