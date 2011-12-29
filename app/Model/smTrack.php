<?php

class smTrack extends AppModel {
  public $name = 'Track';
  public $validate = array(
   'id' => array(
     'rule' => 'notEmpty', // @todo create our own rule for determining a valid spotify uri
   ),
   'title' => array(
    'rule' => 'notEmpty',
   ),
   'artist' => array(
    'rule' => 'notEmpty',
   ),
   'album' => array(
    'rule' => 'notEmpty',
   ),
  );

}
