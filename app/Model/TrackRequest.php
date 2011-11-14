<?php

class TrackRequest extends AppModel {
  public $name = 'Track Request';
   public $validate = array(
    'uri' => array(
      'rule' => 'notEmpty', // @todo create our own rule for determining a valid spotify uri
    ),
   );
}
