<?php
class Bean {

  public function assignFrom($data) {

    foreach($data as $k => $v) {

        $method = 'set'.ucfirst($k);

      if(method_exists($this, $k)) {

        $this->$k($v);
      }
      else if(property_exists($this, $k)) {

        $this->$k = $v;
      }
    }
  }
}
