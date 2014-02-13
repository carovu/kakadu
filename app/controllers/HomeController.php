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

    /**
     * Display the feature screen.
     * 
     * Always accessable by user, wheter he is logged in or not. Feature screen is a listing of all 
     * kakadu functions and traits, that can't be listed on the main page.
     *
     */
    public function getFeature() {
        $view = View::make('home.feature');
        $this->layout->content = $view;
    }
}