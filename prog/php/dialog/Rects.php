<?php

namespace vy;

/// diszjunkt téglalapokból álló terület
class Rects {

   /// új terület egy téglalap alapján
   static function byRect( Rect $r ) {
      $ret = new Rects();
      if ( ! $r->empty() )
         $ret->addRect( $r );
      return $ret;
   }

   static function rectMinus( Rect $a, Rect $b ) {
      $ret = new Rects();
      if ( ! $i = Rect::intersect( $a, $b ))
         return $ret;
      if ( $a->top < $i->top ) {
         $ret->addRect( new Rect($a->left, $a->top,
            $a->width, $i->top - $a->top ));
      }
      if ( $a->left < $i->left ) {
         $ret->addRect( new Rect($a->left, $i->top,
            $i->left - $a->left, $i->height ));
      }
      if ( $i->right() < $a->right() ) {
         $ret->addRect( new Rect($ir->right(), $i->top,
            $a->right() - $i->right(), $i->height));
      }
      if ( $i->bottom() < $a->bottom() ) {
         $ret->addRect( new Rect($a->left, $ir->bottom(),
            $a->width, $a->bottom()-$i->bottom()));
      }
      return $ret;
   }


   /// metszet képzés
   static function intersect( Rects $a, Rects $b ) {
      $ret = new Rects();
      foreach ( $a->rects() as $ar ) {
         foreach ( $b->rects() as $br ) {
            if ( $ir = Rect::intersect( $ar, $br ))
               $ret->addRect( $ir );
         }
      }
      return $ret;
   }

   protected $rects;

   function __construct() {
      $this->clear();
   }

   function empty() { return ! $this->rects; }

   function rects() { return $this->rects; }

   function clear() { $this->rects = []; }

   function __clone() {
      foreach ($this->rects as $k=>$v)
         $this->rects[$k] = clone $v;
   }

   /// egy téglalap hozzáuniózása
   function unionRect( Rect $rect ) {
      if ( $rect->empty() ) return;
      foreach ( $this->rects as $r ) {
         if ( $rect->overlaps( $r ) ) {
            if ( $r->covers( $r )) return;
            $rps = self::rectMinus( $rect, $r );
            foreach ( $rps->rects() as $q )
               $this->unionRect($q);
            return;
         }
      }
      $this->addRect( clone $rect );
   }

   /// egy téglalap kivonása (az eredmény a metszet)
   function cutRect( Rect $rect ) {
      $ret = new Rects();
      $i = count($this->rects)-1;
      while ( 0 <= $i ) {
         $r = $this->rects[$i];
         if ( Rect::intersect( $rect, $r ) ) {
            $rps = $r->cut( $rect );
            $ret->add( $rps );
            if ( $r->empty() ) {
               array_splice( $this->rects, $i, 1 );
               continue;
            }
         }
         --$i;
      }
      return $ret;
   }

   /// metszés egy téglalappal
   function intersectRect( Rect $rect ) {
      $i = count($this->rects)-1;
      while ( 0 <= $i && $this->rects ) {
         $r = $this->rects[$i];
         if ( ! $rect->overlaps( $r )) {
            $this->remove($i);
         } else if ( ! $r->covers( $rect )) {
            if ( $s = Rect::intersect( $rect, $r ))
               $this->rects[$i] = $s;
               else $this->remove($i);
         }
         --$i;
      }
   }

   function __toString() {
      $ret = [];
      foreach ( $this->rects as $r )
         $ret [] = "$r";
      return implode(";",$ret);
   }

   /// új téglalap hozzáadása
   protected function addRect( Rect $r ) {
      $this->rects [] = $r;
   }

   /// egy téglalap törlése
   protected function remove($i) {
      array_splice( $this->rects, $i, 1 );
   }      



}

