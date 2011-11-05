<?php

class RequestsController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Requests';

    function index() {
        // if it's a POST, delete selected requests, or clear all
        $this->set('requests', $this->Request->find('all'));
    }
    
    function add($uri) {
      // if  it's a valid spotify uri, add/update the request model
      // tellt eh user the track was added
      // redirect to the home page
    }
    
    function delete($uri) {
      // tell the user the request was removed
      // redirect to the home page
    }
}
