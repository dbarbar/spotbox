<?php

class SpotifyTracksController extends AppController {
    public $helpers = array ('Html','Form');
    public $name = 'Spotify Tracks';

    function index() {
        $this->set('spotify_tracks', $this->SpotifyTrack->find('all'));
    }
}