<?php
class Bean {

  public function assignFrom($data) {

    foreach($data as $k => $v) {

      if(property_exists($this, $k)) {

        $this->$k = $v;
      }
    }
  }
}
