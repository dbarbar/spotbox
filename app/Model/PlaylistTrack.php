<?php

class PlaylistTrack extends AppModel {
  public $name = 'PlaylistTrack';
  public $belongsTo = array(
    'Track' => array(
      'className' => 'smTrack',
      'foreignKey' => 'track_id',
    )
  );
}
