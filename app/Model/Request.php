<?php

class Request extends AppModel {
  public $name = 'Request';
   public $validate = array(
    'uri' => array(
      'rule' => 'notEmpty', // @todo create our own rule for determining a valid spotify uri
    ),
   );
}
