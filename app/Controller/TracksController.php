<?php

class TracksController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Tracks';

    public function index() {
        $this->set('tracks', $this->Track->find('all'));
    }
    
    public function search() {
      // show a search box - view snippet
      $this->Set('results', array());
      if ($q = $this->request->query['q']) {
        // do a spotify api call and set the results to the view
        $this->Set('results', $this->_spotify_search($q));
        // save the results to the tracks model
      }
    }
    
    public function autocomplete($str = NULL) {
      if ($str) {
        // query the spotifytrack model
        // set the results for a view that returns json
      }
    }
    
    private function _local_search($search) {
      // queries our local spotifytracks model.
      // returns an array of results.
    }
    
  private function _spotify_search($q) {
    // queries the spotify metadata api.
    // returns an array of results.
    require_once('../../vendors/Metatune.php');
    $spotiy = MetaTune::getInstance();
    $response = $spotiy->searchTrack($q);
    if (count($response) < 1) {
      return array();
    }
    if (isset($response['errorid'])) {
      //echo "<pre>Error: " . $response['errorid'] . "\nMsg: " . $response['errormsg'] . "</pre>";
      return NULL;
    }
    return $response;
  }
    
  private function _dual_search($search) {
    // searches local and spotify
    // dedupes the results
    // and places local results above spotify results.
  }
}
