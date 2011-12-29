<?php

class TrackRequestsController extends AppController {
  public $helpers = array ('Html','Form');
  public $name = 'TrackRequests';
  public $components = array('Session');
  public $uses = array('TrackRequest', 'PlaylistTrack', 'smTrack');

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
    else if ($this->PlaylistTrack->find('count', array('conditions' => array('PlaylistTrack.track_id' => $uri))) > 0) {
      $this->Session->setFlash('This track is already in the playlist. Try again later.');
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

    // First, we repopulate the playlist with the newset one from Spotify.
    $this->_spotify_populate_playlist();
    
    // Then we get metadata on a few tracks in the playlist.
    // Only a few because this can be slow and Spotify has rate limits.
    $this->_spotify_populate_playlist_data();

    // Then we find pending requests and send them.
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
/*
  public function test() {
    //$this->_spotify_populate_playlist();
    //$this->_spotify_populate_playlist_data();
  }
*/
  private function _spotify_populate_playlist_data() {
    $params = array(
      'conditions' => array(
        'Track.title' => NULL,
      ),
      'order' => array(
        'PlaylistTrack.id',
      ),
      'limit' => 5,
    );
    $t = $this->PlaylistTrack->find('all', $params);
    if (count($t) < 1) {
      return;
    }
    
    App::import('Vendor', 'MetatuneConfig', array('file' => 'metatune' . DS . 'config.php'));
    App::import('Vendor', 'MetatuneClass', array('file' => 'metatune' . DS . 'MetaTune.class.php'));
    App::import('Vendor', 'MetatuneMBSimpleXMLElement', array('file' => 'metatune' . DS . 'MBSimpleXMLElement.class.php'));
    App::import('Vendor', 'MetaTuneException', array('file' => 'metatune' . DS . 'MetaTuneException.class.php'));
    App::import('Vendor', 'MetatuneSpotifyItem', array('file' => 'metatune' . DS . 'SpotifyItem.class.php'));
    App::import('Vendor', 'MetatuneArtist', array('file' => 'metatune' . DS . 'Artist.class.php'));
    App::import('Vendor', 'MetatuneAlbum', array('file' => 'metatune' . DS . 'Album.class.php'));
    App::import('Vendor', 'MetatuneTrack', array('file' => 'metatune' . DS . 'Track.class.php'));
    
    $spotify = MetaTune::getInstance();
    
    
//    var_export($t);
    foreach ($t as $r) {
//      var_export($r['PlaylistTrack']['track_id']);
      $track = $spotify->lookupTrack($r['PlaylistTrack']['track_id']);
//      var_export($track);
      $this->smTrack->create();
      $this->smTrack->set(
        array(
          'id' => $track->getURI(),
          'title' => $track->getTitle(),
          'artist' => $track->getArtistAsString(),
          'album' => $track->getAlbum(),
          'length' => $track->getLengthInMinutesAsString(),
          'popularity' => $track->getPopularityAsPercent(),
        ));
      $this->smTrack->save();
    }
  }
  
  /**
   * Retrieves the playlist from Spotify and fills in the Model.
   */
  private function _spotify_populate_playlist() {
    // empty the playlist
    $this->PlaylistTrack->deleteAll('TRUE', FALSE, FALSE);
//    $r = file_get_contents(TMP . '/tracks.json');

    // Get the current playlist from Spotify
    $playlist = 'spotify:user:dbarbar:playlist:6kcyifbIr4HEdCxpmLF3yi';
    $host = 'localhost:1337';
    $url = 'http://' . $host . '/playlist/' . $playlist;

  	$ch = curl_init(); 
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable 
  	curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after $timeout secs 
  	curl_setopt($ch, CURLOPT_POST, 0); // using GET

  	$result = curl_exec($ch); // run the whole process 

  	curl_close($ch);

    $r = json_decode($result);
//    print_r($r->tracks);
    $data = array();
    foreach ($r->tracks as $i => $track) {
      $data[] = array('id' => $i, 'track_id' => $track);
    }
    $options = array('fieldList' => array('id', 'track_id'));
    $this->PlaylistTrack->saveMany($data, $options);
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
