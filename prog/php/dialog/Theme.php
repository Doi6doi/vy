<?php

namespace vy;

/// téma (színek, stílus)
class Theme {

   protected static $default;

   static function byOwner( View $owner ) {
      $ret = new Theme();
      $ret->owner = $owner;
      return $ret;
   }

   static function default() {
      if ( ! self::$default ) {
         $d = new Theme();
         $d->proto = false;
         $d->__set(self::BACK, "#888");
         $d->__set(self::BORDER,"#444");
         $d->__set(self::FORE, "#aaa" );
         $d->__set(self::HIGH, "#ccc" );
         $d->__set(self::TEXT, "#000" );
         $d->gap = 4;
         self::$default = $d;
      }
      return self::$default;
   }

   const
      BACK = "back",
      BORDER = "border",
      FORE = "fore",
      GAP = "gap",
      HIGH = "high",
      PROTO = "proto",
      TEXT = "text";

   const
      ALL = [self::BACK,self::BORDER,self::TEXT,self::FORE];

   /// tulajdonos
   protected $owner;
   /// ezen a témán alapszik
   protected $proto;
   /// kitöltések
   protected $fills;
   /// tollak
   protected $pens;
   /// hézag ez elemek között
   protected $gap;

   protected function __construct() {
      $this->fills = [];
      $this->pens = [];
   }

   function __get($fld) {
      if ( self::PROTO == $fld ) return $this->proto;
      if ($this->proto) return $this->proto->__get($fld);
      switch ($fld) {
         case self::BACK:
         case self::FORE:
            return Tools::g( $this->fills, $fld );
         case self::BORDER:
         case self::HIGH:
         case self::TEXT:
            return Tools::g( $this->pens, $fld );
         case self::GAP: return $this->gap;
         case self::PROTO:
         default: throw Tools::unFld($fld);
      }
   }

   function __set($fld,$x) {
      if ( $this->__get($fld) == $x ) return;
      if ( self::PROTO == $fld )
         return $this->proto = $x;
      $this->cow();
      switch ($fld) {
         case self::BACK:
         case self::FORE:
            $this->fills[$fld] = $this->asFill($x);
         break;
         case self::BORDER:
         case self::HIGH:
         case self::TEXT:
            $this->pens[$fld] = $this->asPen($x);
         break;
         case self::GAP: $this->gap = $x; break;
         default: throw Tools::unFld($fld);
      }
      if ($this->owner)
         $this->owner->invalidate();
   }

   function build( $d ) {
      Tools::buildProps( $this, $d, self::ALL );
   }

   function __toString() {
      $ret = "Theme[";
      if (false === $this->proto ) {
         $ret .= sprintf("back:%s,fore:%s,gap:%d]",
            $this->back, $this->fore, $this->gap );
      } else if ( $this->proto ) {
         $ret .= "$this->proto]";
      } else {
         $ret .= "null]";
      }
      return $ret;
   }

   /// írás előtt új téma készítés
   protected function cow() {
      if (null === $p = $this->proto)
         $p = self::default();
      if (false !== $p) {
         $this->proto = false;
         foreach (self::ALL as $fld)
            $this->__set($fld, $p->__get($fld));
         $this->gap = $p->gap;
      }
   }

   /// kitöltésként értve
   protected function asFill($x) {
      if (is_string($x))
         $x = Color::parse($x);
      if (is_object($x)) {
         if ($x instanceof Color)
            return Fill::byColor($x);
         if ($x instanceof Fill)
            return $x;
         throw new EVy("Unknown fill: ".get_class($x));
      }
      throw new EVy("Unknown fill: $x");
   }

   /// tollként értve
   protected function asPen($x) {
      if (is_string($x))
         $x = Color::parse($x);
      if (is_object($x)) {
         if ($x instanceof Color)
            return Pen::byColor($x);
         if ($x instanceof Pen)
            return $x;
         throw new EVy("Unknown pen: ".get_class($x));
      }
      throw new EVy("Unknown pen: $x");
   }

}
