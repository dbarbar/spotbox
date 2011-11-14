<?php

class Track extends AppModel {
  public $name = 'Track';
  public $validate = array(
   'uri' => array(
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