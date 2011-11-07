<?php

class RequestsController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Requests';
    public $components = array('Session');

    public function index() {
        // if it's a POST, delete selected requests, or clear all
        $this->set('requests', $this->Request->find('all'));
    }
    
    public function add($uri) {
      // if  it's a valid spotify uri, add/update the request model
      if ($this->_is_valid_uri($uri)) {
        $this->Request->save($uri);
        $this->Session->setFlash('Your song request has been added.');
      }
      $this->redirect(array('controller' => 'SpotifyTracks', 'action' => 'search'));
    }

    public function delete($uri) {
      if (!$this->request->is('post')) {
        throw new MethodNotAllowedException();
      }
      if ($this->Request->delete($uri)) {
        $this->Session->setFlash("The request for $uri has been deleted.");
        $this->redirect(array('action' => 'index'));
      }
    }
    
    private function _is_valid_uri($uri) {
      /**
       * @todo determine if the string given is a valid spotify track uri.
       */
       if ($uri == 'spotify:track:1234') {
         return TRUE;
       }
       else {
         return FALSE;
       }
    }
}
