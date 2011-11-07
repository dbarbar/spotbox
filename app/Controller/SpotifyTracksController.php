<?php

class SpotifyTracksController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Spotify Tracks';

    public function index() {
        $this->set('spotify_tracks', $this->SpotifyTrack->find('all'));
    }
    
    public function search($search = NULL) {
      // show a search box - view snippet
      if ($search) {
        // do a spotify api call and set the results to the view
        // save the results to the spotifytrack model
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
    
    private function _spotify_search($search) {
      // queries the spotify metadata api.
      // returns an array of results.
    }
    
    private function _dual_search($search) {
      // searches local and spotify
      // dedupes the results
      // and places local results above spotify results.
    }
}
