<?php

class TrackRequestsController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'TrackRequests';
    public $components = array('Session');

    public function index() {
        $this->set('requests', $this->TrackRequest->find('all'));
    }
    
    public function textlist() {
        $results = $this->TrackRequest->find('all');
        $rows = array();
        foreach ($results as $row) {
          $rows[] = $row['TrackRequest']['id'];
        }

        $this->response->type('text/plain');
        $this->response->disableCache();
        $this->response->body(implode("\n", $rows));
        // won't look for the view
        $this->autoRender = FALSE;
    }
    
    public function add($uri) {
      // if it's a valid spotify uri, add/update the request model
      if ($this->_is_valid_uri($uri)) {
        $this->TrackRequest->id = $uri;
        if ($this->TrackRequest->read() === FALSE) {
          // new request
          $this->TrackRequest->save(array('id' => $uri));
        }
        else {
          // already exists
          /**
           * @todo figure out the request count incrementing.
           */
          $this->TrackRequest->save(array('id' => $uri, 'request_count' => 2));
        }        
        $this->Session->setFlash('Your song request has been added.');
      }
      $this->redirect('/');
    }

    public function clear_all() {
      $this->TrackRequest->deleteAll('1 = 1');
      $this->Session->setFlash("The play queue has been cleared.");
      $this->redirect(array('action' => 'index'));
    }
    
    private function _is_valid_uri($uri) {
      return TRUE;
      /**
       * @todo determine if the string given is a valid spotify track uri.
       *
       if ($uri == 'spotify:track:1234') {
         return TRUE;
       }
       else {
         return FALSE;
       }
       */
    }
}
