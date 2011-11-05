<?php

class SpotifyTracksController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Spotify Tracks';

    function index() {
        $this->set('spotify_tracks', $this->SpotifyTrack->find('all'));
    }
    
    function search($search = NULL) {
      // show a search box
      if ($search) {
        // do a spotify api call and set the results to the view
        // save the results to the spotifytrack model
      }
    }
    
    function autocomplete($str = NULL) {
      if ($str) {
        // query the spotifytrack model
        // set the results for a view that returns json
      }
    }
}