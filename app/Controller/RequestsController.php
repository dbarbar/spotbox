<?php

class RequestsController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Requests';

    function index() {
        $this->set('requests', $this->Request->find('all'));
    }
}