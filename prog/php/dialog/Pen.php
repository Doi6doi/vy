<?php

namespace vy;

class Pen {

   static function byColor( Color $c ) {
      $ret = new Pen();
      $ret->color = $c;
      return $ret;
   }

   public $color;

   protected function __construct() { }

   function __toString() { return "$this->color"; }

}
