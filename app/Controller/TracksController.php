<?php

class TracksController extends AppController {
  public $helpers = array ('Html','Form');
  public $name = 'Tracks';
  public $uses = array('smTrack', 'PlaylistTrack');

  public function index() {
      $this->set('tracks', $this->PlaylistTrack->find('all', array('limit' => 200, 'order' => 'PlaylistTrack.id')));
  }

/**
 * @todo create an album controller and lookup action.
 * @todo create an artist controller and lookup action.
 */
/*
  public function lookup() {
    if (!isset($this->request->query['q'])) {
      $this->redirect('/');
    }    
    $this->Set('tracks', array());
  }
*/
  public function search() {
    $this->Set('results', array());
    $this->Set('tracks', array());
    $this->Set('q', NULL);
    if (isset($this->request->query['q']) && $this->request->query['q'] != '') {
      $q = $this->request->query['q'];
      $type = 'track';
      if (isset($this->request->query['type'])) {
        $type = $this->request->query['type'];
        if (!in_array($type, array('track', 'album'))) {
          $type = 'track';
        }
      }
      
      $initial_results = $this->_spotify_search($q, $type);
      if ($type == 'album') {
        $this->Set('album_name', $initial_results->getName());
        $initial_results = $initial_results->getTracks();
      }
      $results = array();
      if (count($initial_results) > 0) {
        foreach ($initial_results as $track) {
          if (!$track->getAlbum()->isAvailable('US')) {
            continue;
          }
        
          if ($this->PlaylistTrack->find('count', array('conditions' => array('PlaylistTrack.track_id' => $track->getURI()))) > 0) {
            $requested = TRUE;
          }
          else {
            $requested = FALSE;
          }

          $results[] = array(
            'requested' => $requested,
            'uri' => $track->getURI(),
            'title' => $track->getTitle(),
            'artist' => $track->getArtistAsString(),
            'album' => $track->getAlbum(),
          );
        }
      }
      $this->Set('results', $results);
      $this->Set('q', $q);
    }
    else {
      $this->set('tracks', $this->PlaylistTrack->find('all', array('conditions' => array('Track.title IS NOT NULL'), 'limit' => 10, 'order' => 'PlaylistTrack.id DESC')));
    }
  }

  /**
   * @todo the autocomplete for search.
   */
/*  public function autocomplete($str = NULL) {
    if ($str) {
      // query the track model
      // set the results for a view that returns json
    }
  }*/

  /**
   * @todo a local search that only queries our tracks model.
   * Use this for the autocomplete to get previously requested tracks.
   */
/*  private function _local_search($search) {
    // queries our local spotifytracks model.
    // returns an array of results.
  } */

  /**
   * Loads up MetaTune and searches Spotify for tracks.
   * Returns an array of MetaTune Track objects.
   * Returns an empty array for zero results.
   * Returns NULL on error.
   */
  private function _spotify_search($q, $type = 'track') {
    App::import('Vendor', 'MetatuneConfig', array('file' => 'metatune' . DS . 'config.php'));
    App::import('Vendor', 'MetatuneClass', array('file' => 'metatune' . DS . 'MetaTune.class.php'));
    App::import('Vendor', 'MetatuneMBSimpleXMLElement', array('file' => 'metatune' . DS . 'MBSimpleXMLElement.class.php'));
    App::import('Vendor', 'MetaTuneException', array('file' => 'metatune' . DS . 'MetaTuneException.class.php'));
    App::import('Vendor', 'MetatuneSpotifyItem', array('file' => 'metatune' . DS . 'SpotifyItem.class.php'));
    App::import('Vendor', 'MetatuneArtist', array('file' => 'metatune' . DS . 'Artist.class.php'));
    App::import('Vendor', 'MetatuneAlbum', array('file' => 'metatune' . DS . 'Album.class.php'));
    App::import('Vendor', 'MetatuneTrack', array('file' => 'metatune' . DS . 'Track.class.php'));

    $spotify = MetaTune::getInstance();
    
    switch ($type) {
      case 'album':
        $response = $spotify->searchAlbum($q);
        break;
      default:
        $response = $spotify->searchTrack($q);
        break;
    }

    if (count($response) < 1) {
      return array();
    }
    if (isset($response['errorid'])) {
      //echo "<pre>Error: " . $response['errorid'] . "\nMsg: " . $response['errormsg'] . "</pre>";
      return NULL;
    }
    return $response;
  }

  /**
   * @todo A dual search would search both locally and Spotify.
   * And weighs results that are in both higher since it's a track that's been requrested before.
   */
  private function _dual_search($search) {
    // searches local and spotify
    // dedupes the results
    // and places local results above spotify results.
  }

}
