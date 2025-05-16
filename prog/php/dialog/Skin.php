<?php

namespace vy;

/// kirajzolási mód (stílus)
class Skin {

   protected static $default;

   static function byOwner( View $owner ) {
      $ret = new Skin();
      $ret->owner = $owner;
      return $ret;
   }

   static function default() {
      if ( ! self::$default ) {
         $d = new Skin();
         $d->proto = false;
         self::$default = $d;
      }
      return self::$default;
   }

   const
      PROTO = "proto";

   /// tulajdonos
   protected $owner;
   /// ezen a skinen alapszik
   protected $proto;

   protected function __construct() { }

   function __get($fld) {
      if ( self::PROTO == $fld ) return $this->proto;
      if ($this->proto) return $this->proto->__get($fld);
      if (null === $this->proto) return self::default()->__get($fld);
      switch ($fld) {
         case self::PROTO: return $this->proto;
         default: throw Tools::unFld($fld);
      }
   }

   function __set($fld,$x) {
      if ( $this->__get($fld) == $x ) return;
      if ( self::PROTO == $fld )
         return $this->proto = $x;
      $this->cow();
      switch ($fld) {
         default: throw Tools::unFld($fld);
      }
      if ($this->owner)
         $this->owner->invalidate();
   }

   function build( $d ) {
   }

   /// keret mérete
   function border( View $v ) {
      if ( $v instanceof Button )
         return Borders::default();
      else
         return Borders::none();
   }

   function paintBackground( Canvas $c, View $v ) {
      if ($this->proto)
         return $this->proto->paintBackground( $c, $v );
      return $this->doPaintBackground( $c, $v );
   }

   function paintText( Canvas $c, View $v, Point $at, string $txt ) {
      if ($this->proto)
         return $this->proto->paintText( $c, $v, $at, $txt );
      return $this->doPaintText( $c, $v, $at, $txt );
   }

   function paintButton( Canvas $c, View $v ) {
      if ($this->proto)
         return $this->proto->paintButton( $c, $v );
      return $this->doPaintButton( $c, $v );
   }

   function paintBorder( Canvas $c, View $v ) {
      if ($this->proto)
         return $this->proto->paintBorder( $c, $v );
      return $this->doPaintBorder( $c, $v );
   }

   function __toString() {
      $ret = "Skin[";
      if (false === $this->proto ) {
         $ret .= sprintf("buttonBorders:%s]",
            $this->buttonBorders );
      } else if ( $this->proto ) {
         $ret .= "$this->proto]";
      } else {
         $ret .= "null]";
      }
      return $ret;
   }


   protected function viewRect( View $v ) {
      return new Rect(0,0,$v->width,$v->height);
   }

   protected function doPaintBackground( Canvas $c, View $v ) {
      VypGui::gui()->fillRect( $c, $this->viewRect($v), $v->theme->back );
   }

   protected function doPaintText( Canvas $c,
      View $v, Point $at, string $txt )
   {
      VypGui::gui()->drawText( $c, $at, $txt, $v->font, $v->theme->text );
   }

   protected function doPaintButton( Canvas $c, View $v ) {
      VypGui::gui()->fillRect( $c, $this->viewRect($v), $v->theme->fore );
      $this->doPaintBorder( $c, $v );
   }

   protected function doPaintBorder( Canvas $c, View $v ) {
      $p = $v->hovered ? $v->theme->high : $v->theme->border;
      VypGui::gui()->drawRect( $c, $this->viewRect($v), $p );
   }

}
