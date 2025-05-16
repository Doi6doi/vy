<?php

namespace vy;

class Rect {

   static function intersect( Rect $a, Rect $b ) {
      $il = max( $a->left, $b->left );
      $ir = min( $a->right(), $b->right() );
      if ( $ir <= $il ) return null;
      $it = max( $a->top, $b->top );
      $ib = min( $a->bottom(), $b->bottom() );
      if ( $ib <= $it ) return null;
      return new Rect( $il, $it, $ir-$il, $ib-$it );
   }

   static function move( Rect $r, Point $d ) {
      return new Rect( $r->left+$d->x, $r->top+$d->y, $r->width, $r->height );
   }

   public $left, $top, $width, $height;

   function __construct( $left, $top, $width, $height ) {
      $this->build( [$left, $top, $width, $height] );
   }

   function empty() { return 0 == $this->width * $this->height; }

   function right() { return $this->left + $this->width; }

   function bottom() { return $this->top + $this->height; }

   function build( $d ) {
      if ( ! $d ) return;
      if ( is_array( $d )) {
         switch ( count($d) ) {
            case 2:
               $this->build([0,0,$d[0],$d[1]]);
            return;
            case 4:
               $this->left = $d[0];
               $this->top = $d[1];
               $this->width = max(0,$d[2]);
               $this->height = max(0,$d[3]);
            return;
            default:
         }
      }
      throw new EVy("Cannot build rect by:".Tools::flatten($d));
   }

   function contains( Point $p ) {
      return $this->left <= $p->x && $p->x < $this->right()
         && $this->top <= $p->y && $p->y < $this->bottom();
   }

   function equals( Rect $o ) {
      return $this->left == $o->left
         && $this->top == $o->top
         && $this->width == $o->width
         && $this->height == $o->height;
   }

   /// van-e átfedés a két téglalap között
   function overlaps( Rect $o ) {
      return Tools::overs( $this->left, $this->right(), $o->left, $o->right())
         && Tools::overs($this->top,$this->bottom(), $o->top, $o->bottom() );
   }
   
   /// letakarja-e ez a téglalap a másikat
   function covers( Rect $o ) {
      return $this->left <= $o->left && $o->right() <= $this->right()
         && $this->top <= $o->top && $o->bottom() <= $this->bottom();
   }
   
   function __toString() {
      return sprintf("[%d,%d;%d,%d]",
         $this->left, $this->top, $this->width, $this->height);
   }


}
