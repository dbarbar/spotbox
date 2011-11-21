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
      if (!$this->_is_valid_track_uri($uri)) {
        $this->Session->setFlash('Invalid Track URI');
      }
      else {
        // Save the request locally.
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
        $this->Session->setFlash('Your request has been added. Woohoo!');
      }
      $this->redirect('/');
    }

    public function clear_all() {
      $this->TrackRequest->deleteAll('1 = 1');
      $this->Session->setFlash("The play queue has been cleared.");
      $this->redirect(array('action' => 'index'));
    }
    
    /**
     * Retrieve the added requests from our model
     * and batch send them to Spotify.
     * Then remove successful adds from our request model.
     */
    public function cron() {
      $results = $this->TrackRequest->find('all');
      $tracks = array();
      foreach ($results as $row) {
        $tracks[] = $row['TrackRequest']['id'];
      }
      
      // talk to Spotify and add the tracks to the playlist.
      $result = $this->_spotify_add_tracks($tracks);
      $success = TRUE;
      $error = array();
      if ($result === FALSE) {
        $error[] = 'Uh oh.  Didn\'t get a response from the local Spotify Playlist API Server. Tell David to fix it.';
        $success = FALSE;
      }
      else {
        $result = json_decode($result);
        
        if (isset($result->message)) {
          $error[] = 'Message Response from the local Spotify Server.  Send this to David: ' . $result->message;
          $success = FALSE;
        }
        
        if (isset($result->tracks)) {
          $valid_tracks_added = 0;
          foreach ($tracks as $track) {
            if (in_array($track, $result->tracks)) {
              $valid_tracks_added++;
              $this->TrackRequest->delete($track);
            }
            else {
              $success = FALSE;
              $error[] = 'Track ' . $track . ' not found in the playlist returned.';
            }
          }
        }
      }
      
      $this->response->type('text/plain');
      $this->response->disableCache();
      if (!$success) {
        $this->response->body(implode("\n", $error));
      }
      // won't look for the view
      $this->autoRender = FALSE;
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
  		curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable 
  		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after $timeout secs 
  		curl_setopt($ch, CURLOPT_POST, 1); // set POST method 

  		$body = json_encode($tracks);

  		curl_setopt($ch, CURLOPT_POSTFIELDS, $body); // add POST fields 
  		$result = curl_exec($ch); // run the whole process 

  		curl_close($ch);
  		return $result;
    }

  private function _is_valid_track_uri($uri) {
    $pattern = '/^spotify:track:(A-Za-z0-9)*/';
    if (preg_match($pattern, $uri)) {
      return TRUE;
    }
    return FALSE;
  }
}
