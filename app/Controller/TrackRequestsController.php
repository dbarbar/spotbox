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
      if ($this->_is_valid_track_uri($uri)) {
        $result = $this->_spotify_add_tracks(array($uri));
/*        $this->TrackRequest->id = $uri;
        if ($this->TrackRequest->read() === FALSE) {
          // new request
          $this->TrackRequest->save(array('id' => $uri));
        }
        else {
          // already exists
          /**
           * @todo figure out the request count incrementing.
           *
          $this->TrackRequest->save(array('id' => $uri, 'request_count' => 2));
        }
*/
        if ($result === FALSE) {
          $this->Session->setFlash('Uh oh.  Could not connect to the local Spotify Playlist API Server. Tell David to restart it.');
        }
        else {
          $result = json_decode($result);
          
          if (isset($result->message)) {
            $this->Session->setFlash('Message Response from the local Spotify Server.  Send this to David: ' . $result->message);
          }
          
          if (isset($result->tracks) && in_array($uri, $result->tracks)) {
            $this->Session->setFlash('Your request has been added to the playlist. Woohoo!');
          }
        }
      }
      $this->redirect('/');
    }

    public function clear_all() {
      $this->TrackRequest->deleteAll('1 = 1');
      $this->Session->setFlash("The play queue has been cleared.");
      $this->redirect(array('action' => 'index'));
    }
    
    /**
     * @todo only update playlist on a schedule.
     */
/*    public function cron() {
      $tracks = array();
      $r = $this->TrackRequest->find('all');
      foreach ($r as $s) {
        $tracks[] = $s['TrackRequest']['id'];
      }
      
      if (count($tracks) > 0) {
        var_export($tracks); exit();
//      $result = $this->_spotify_add_tracks($tracks);
        if ($result !== FALSE) {
          
        }
      }
      $this->autoRender = FALSE;
    }
*/    
    private function _spotify_add_tracks($tracks) {
      $playlist = 'spotify:user:dbarbar:playlist:6kcyifbIr4HEdCxpmLF3yi';
      $host = 'localhost:1337';
      $url = 'http://' . $host . '/playlist/' . $playlist . '/add';
      // POST /playlist/{id}/add?index

  		$ch = curl_init(); 
  		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to 
  		curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
  		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects 
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
  		curl_setopt($ch, CURLOPT_TIMEOUT, 60); // times out after $timeout secs 
  		curl_setopt($ch, CURLOPT_POST, 1); // set POST method 

  		$body = json_encode($tracks);

  		curl_setopt($ch, CURLOPT_POSTFIELDS, $body); // add POST fields 
  		$result = curl_exec($ch); // run the whole process 

  		curl_close($ch);
  		return $result;
    }

    private function _is_valid_track_uri($uri) {
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
