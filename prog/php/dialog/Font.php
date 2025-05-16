<?php

namespace vy;

class Font {

   const
      FAST = "fast",
      FILENAME = "fileName",
      IMPL = "impl",
      PROTO = "proto",
      SIZE = "size";

   static function byOwner( View $owner ) {
      $ret = new Font();
      $ret->owner = $owner;
      return $ret;
   }

   static function byFile( $fname, $size ) {
      $ret = new Font();
      $ret->proto = false;
      $ret->fname = $fname;
      $ret->size = $size;
      $ret->fast = false;
      VypGui::gui()->createFont( $ret );
      return $ret;
   }

   static function default() {
      return VypGui::gui()->defaultFont();
   }

   protected $proto;
   protected $owner;
   protected $fname;
   protected $impl;
   protected $size;
   protected $fast;

   protected function __construct() {
      $this->fast = false;
   }

   function __destruct() {
      if ($this->impl)
         Vypgui::gui()->destroyFont($this);
   }

   function __get($fld) {
      if ( self::PROTO == $fld ) return $this->proto;
      if ($this->proto) return $this->proto->__get($fld);
      if (null === $this->proto) return self::default()->__get($fld);
      switch ($fld) {
         case self::FAST: return $this->fast;
         case self::FILENAME: return $this->fname;
         case self::IMPL: return $this->impl;
         case self::SIZE: return $this->size;
         default: throw Tools::unFld($fld);
      }
   }

   function __set($fld,$x) {
      if ( $this->__get($fld) == $x ) return;
      if ( self::PROTO == $fld )
         return $this->proto = $x;
      $this->cow();
      switch ($fld) {
         case self::FAST: $this->fast = $x; break;
         case self::FILENAME: $this->fname = $x; break;
         case self::IMPL: $this->impl = $x; break;
         case self::SIZE: $this->size = $x; break;
         default: throw Tools::unFld($fld);
      }
      VypGui::gui()->updateFont($this,$fld);
      if ( $this->owner )
         $this->owner->invalidate();
   }

   function build( $d ) {
      Tools::buildProps($this,$d,
         [self::FAST,self::FILENAME,self::SIZE] );
   }

   function __toString() {
      $ret = "Font[";
      if (false === $this->proto ) {
         $ret .= sprintf("fname:%s,size:%d,fast:%d]", 
            $this->fname, $this->size, $this->fast );
      } else if ( $this->proto ) {
         $ret .= "$this->proto]";
      } else {
         $ret .= "null]";
      }
      return $ret;
   }

   /// írás előtt új font készítés
   protected function cow() {
      if (null === $p = $this->proto)
         $p = VypGui::gui()->defaultFont();
      if ($p) {
         $this->proto = false;
         $this->fname = $p->fname;
         $this->size = $p->size;
         $this->fast = $p->fast;
         VypGui::gui()->createFont($this);
      }
   }

}
