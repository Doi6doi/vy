<?php

namespace vy;

/// elrendezések
class Layouts {

   /// oldal elrendezés: egymás alatt középen
   static function page( Group $g, $place ) {
      if ( ! $its = $g->items())
         return new Point();
      $gap = $g->theme->gap;
      $ret = null;
      foreach ($its as $v)
         self::incMax( $ret, $v->preferred, $gap );
      if ( $place ) {
         $c = $g->client;
         $half = $c->width / 2;
         $y = ($c->height - $ret->y)/2;
         foreach ( $its as $v ) {
            $p = $v->preferred;
            $v->bounds = new Rect( $half-$p->x/2, $y, $p->x, $p->y );
            $y += $p->y+$gap;
         }
      }
      return $ret;
   }

   /// rács elrendezés oszlopokban
   static function grid( Group $g, $ncol, $place ) {
      $gap = $g->theme->gap;
      $need = null;
      $n = count( $g->items() );
      $nrow = 0;
      $l = new Point();
      for ($i=0; $i<$n; $i+=$ncol) {
         $s = self::gridRow( $g, $i, $ncol, $l, null, null, false );
         self::incMax( $need, $s, $gap );
         ++$nrow;
      }
      $cx = $need->x;
      $need->x = $ncol*($cx+$gap)-$gap;
      if ( ! $place )
         return $need;
      $ret = clone $need;
      $c = $g->client;
      $gcw = $c->width;
      if ( $gcw < $need->x ) {
         $cx = ($gcw - ($ncol-1)*$gap)/$ncol;
         $need->x = $ncol*($cx+$gap)-$gap;
      }
      $cy = null;
      $gch = $c->height;
      if ( $gch < $need->y ) {
         $cy = ($gch - ($nrow-1)*$gap)/$nrow;
         $need->y = $nrow*($cy+$gap)-$gap;
      }
      $l = new Point( ($gcw - $need->x)/2, ($gch - $need->y)/2 );
      for ($i=0; $i<$n; $i+=$ncol)
         self::gridRow( $g, $i, $ncol, $l, $cx, $cy, true );
      return $ret;
   }

   /// a rács egy sorának elrendezése
   protected static function gridRow( Group $g, $at, $ncols, Point $l, $cx, $cy, $place ) {
      $ret = new Point(0+$cx,0+$cy);
      $gap = $g->theme->gap;
      $its = $g->items();
      $max = min( $ncols, count($its)-$at );
      if ( null === $cx || null === $cy ) {
         for ($i=0; $i<$max; ++$i) {
            $ii = $its[$at+$i];
            $p = $ii->preferred;
            if ( null === $cx ) $ret->x = max( $ret->x, $p->x );
            if ( null === $cy ) $ret->y = max( $ret->y, $p->y );
         }
      }
      if ($place) {
         $lx = $l->x;
         for ($i=0; $i<$max; ++$i) {
            $ii = $its[$at+$i];
            $pl = new Rect($l->x, $l->y, $ret->x, $ret->y);
            $ii->bounds = $pl;
            $l->x += $ret->x + $gap;
         }
         $l->y += $ret->y + $gap;
         $l->x = $lx;
      }
      return $ret;   
   }

   /// p.x-en maximum, p.y-en hozzáadás gap-pel
   protected static function incMax( & $p, Point $q, $gap ) {
      if ( null === $p ) {
         $p = clone $q;
      } else {
         $p->x = max( $p->x, $q->x );
         $p->y += $q->y + $gap;
      }
   }
      
}

