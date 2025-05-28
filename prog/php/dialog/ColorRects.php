<?php

namespace vy;

/// színes téglalapok a Curses háttérhez
class ColorRects {

   protected $items;
   
   function __construct() {
      $this->items = [];
   }
   
   function minus( Rect $r ) {
      for ($i=count($this->items)-1; 0<=$i; --$i) {
         $this->items[$i][0]->cutRect( $r );
         if ( $this->items[$i][0]->empty() )
            array_splice( $this->items, $i, 1 );
      }
   }
   
   function add( Rect $r, Color $c ) {
      $this->items [] = [Rects::byRect($r),$c];
   }
   
   /// egy pont háttérszíne
   function get( $x, $y ) {
      $p = new Point( $x, $y );
      foreach ($this->items as $r) {
         if ( $r[0]->contains($p))
            return $r[1];
      }
      return null;
   }

}

