<?php

namespace vy;

class Fill {

   static function byColor( Color $c ) {
      $ret = new Fill();
      $ret->color = $c;
      return $ret;
   }

   public $color;

   protected function __construct() { }

   function __toString() { return "$this->color"; }

}
