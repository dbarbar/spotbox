<?php

class TrackRequest extends AppModel {
  public $name = 'TrackRequest';
   public $validate = array(
    'id' => array(
      'rule' => 'notEmpty', // @todo create our own rule for determining a valid spotify uri
    ),
   );
}
