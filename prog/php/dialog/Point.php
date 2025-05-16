<?php

namespace vy;

class Point {

   static function add( Point $p, Point $q ) {
      return new Point( $p->x + $q->x, $p->y + $q->y );
   }

   public $x, $y;

   function __construct($x=0,$y=0) {
      $this->x = $x;
      $this->y = $y;
   }

   function __toString() {
      return sprintf("%d,%d",$this->x,$this->y );
   }

   function delta( $dx, $dy ) {
      $this->x += $dx;
      $this->y += $dy;
   }

}
