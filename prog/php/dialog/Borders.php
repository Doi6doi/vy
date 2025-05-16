<?php

namespace vy;

class Borders {

   static function default() {
      static $ret = new Borders(4,4,4,4);
      return $ret;
   }

   static function none() {
      static $ret = new Borders();
      return $ret;
   }

   public $left, $top, $right, $bottom;

   function __construct( $left=0, $top=0, $right=0, $bottom=0 ) {
      $this->left = $left;
      $this->top = $top;
      $this->right = $right;
      $this->bottom = $bottom;
   }

   function decrease( Rect $r ) {
      return new Rect( $r->left + $this->left, $r->top + $this->top,
         $r->width - $this->left - $this->right,
         $r->height - $this->top - $this->bottom
      );
   }

   function increase( Point $p ) {
      return new Point( $p->x + $this->left + $this->right,
         $p->y + $this->top + $this->bottom );
   }

   function move( Rect $r ) {
      return new Rect( $r->left + $this->left, $r->top + $this->top,
         $r->width, $r->height );
   }

}
