<?php

namespace vy;

class Canvas {

   protected $window;
   protected $crop;
   protected $offset;

   function __construct(Window $window) {
      $this->window = $window;
      $this->initCrop();
      $this->offset = new Point();
   }

   function sub( Rect $r ) {
      $ret = clone $this;
      $o = $ret->offset;
      $o->delta( $r->left, $r->top );
      $ret->crop->intersectRect( 
         new Rect( $o->x, $o->y, $r->width, $r->height ));
      return $ret;
   }   

   function __clone() {
      $this->crop = clone $this->crop;
      $this->offset = clone $this->offset;
   }

   function window() { return $this->window; }

   function crop() { return $this->crop; }

   function initCrop() {
      $c = $this->window->client;
      $this->crop = Rects::byRect( new Rect( 0,0,$c->width,$c->height ) );
   }

   function cropRects( Rects $o ) {
      $this->crop = Rects::intersect( $this->crop, $o );
   }

   function offset() { return $this->offset; }

}
