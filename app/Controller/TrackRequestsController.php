<?php

class TrackRequestsController extends AppController {
  public $helpers = array ('Html','Form');
  public $name = 'TrackRequests';
  public $components = array('Session');

  /**
   * Provides a list of requrests that have not been added to the playlist.
   */
  public function index() {
      $this->set('requests', $this->TrackRequest->find('all'));
  }

/**
 * @deprecated
 */    
/*    public function textlist() {
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
*/    

  /**
   * Adds a track to our local TrackRequest model.
   */
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

  /**
   * @deprecated
   */
/*    public function clear_all() {
      $this->TrackRequest->deleteAll('1 = 1');
      $this->Session->setFlash("The play queue has been cleared.");
      $this->redirect(array('action' => 'index'));
    }
*/

  /**
   * Retrieve the added requests from our model
   * and batch sends them to the Spotify API Server.
   * Then remove successful adds from our request model.
   * Should be called from cron as something like
   * curl http://spotimonster.com/TrackRequests/cron
   */
  public function cron() {
    $this->response->type('text/plain');
    $this->response->disableCache();
    // won't look for the view
    $this->autoRender = FALSE;

    $results = $this->TrackRequest->find('all');
    // nothing to send.
    if (count($results) < 1) {
      return;
    }
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
        foreach ($tracks as $track) {
          if (in_array($track, $result->tracks)) {
            $this->TrackRequest->delete($track);
          }
          else {
            $success = FALSE;
            $error[] = 'Track ' . $track . ' not found in the playlist returned.';
          }
        }
      }
    }
  
    if (!$success) {
      $this->response->body(implode("\n", $error));
    }
  }

  /**
   * Calls the spotify API server to add an array of uris to the playlist.
   */
  private function _spotify_add_tracks($tracks) {
    $playlist = 'spotify:user:dbarbar:playlist:6kcyifbIr4HEdCxpmLF3yi';
    $host = 'localhost:1337';
    $url = 'http://' . $host . '/playlist/' . $playlist . '/add';
    // ex. POST /playlist/{id}/add?index
    // I rewrote the Spotify API Server to add the tracks
    // to the end of the playlist if no index is given.
    // This was the most common case for my application of the call.
    // By default the API Server sends back an error when no index is given.

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

  /**
   * Very basic string validation of track uris
   * since they can be written into the add URLs.
   */
  private function _is_valid_track_uri($uri) {
    $pattern = '/^spotify:track:(A-Za-z0-9)*/';
    if (preg_match($pattern, $uri)) {
      return TRUE;
    }
    return FALSE;
  }

}
