<?php

namespace vy;

/// kiíró terminál
class Terminal {

   /// fajták
   const
      ANSI = "ansi",
      DUMB = "dumb";

   /// színezés
   const
      C2 = "c2",
      C8 = "c8",
      C256 = "c256",
      C24BIT = "c24bit";

   /// lehetőségek
   const
      TITLE = "title";

   protected static $ins;
   
   /// singleton példány
   static function ins() {
      if ( ! self::$ins )
         self::$ins = self::detect();
      return self::$ins;
   }

   /// fajta észlelés
   static function detect() {
      $ret = new Terminal( self::DUMB, self::C2 );
      switch ( $t = getenv("TERM") ) {
         case "xterm": case "xterm-256color": 
         case "linux": case "screen": case "screen-256color":
         case "ansi":
            $ret->kind = self::ANSI;
         break;
      }
      switch ( Tools::system() ) {
         case Tools::LINUX: case Tools::WINDOWS: 
            $ret->kind = self::ANSI;
         break;
      }
      switch ( $c = getenv("COLORTERM")) {
         case "truecolor": 
         case "24bit": 
            $ret->color = self::C24BIT;
         break;
         default:
            if ( self::ANSI == $ret->kind ) {
               switch ( $t ) {
                  case "xterm-256color":
                  case "screen-256color":
                     $ret->color = self::C256;
                  break;
                  default:
                     $ret->color = self::C8;
               }
            }
      }
      return $ret;
   }

   protected $kind;
   protected $color;

   protected function __construct( $kind, $color ) {
      $this->kind = $kind;
      $this->color = $color;
   }

   function can($f) {
      switch ($f) {
         case self::TITLE: return self::DUMB != $this->kind;
         default: throw new EVy("Unknown feature: $f");
      }
   }
      
   /// ablak cím beállítása
   function setTitle($x) {
      switch ($this->kind) {
         case self::ANSI: return $this->esc( "]0;$x\x07");
         case self::DUMB: return;
         default: throw $this->unKind();
      }
   }
    
   /// háttérszín beállítása
   function setBack($c) {
      return $this->setCol(false,$c);
   }
      
   /// escape szekvencia   
   function esc( $x ) {
      print( "\x1b$x" );
   }

   /// kurzor morzgatás
   function move( $r, $c ) {
      return $this->esc(sprintf("[%d;%dH",$r,$c));
   }

   /// kiírás
   function write($x) {
      print($x);
   }

   protected function setCol($fore,Color $c) {
      switch ($this->color) {
         case self::C2: return;
         case self::C8:
            $i = $this->find8($c);
            $code = (8 <= $i ? ($fore?90:100):($fore?30:40));
            return $this->esc( "[".$code."m" );
         case self::C256:
            $i = $this->find256($c);
            return $this->esc(sprintf( "[%d;5;%dm", $fore?38:48, $i ));
         case self::C24BIT:
            return $this->esc( sprintf( "[%d;2;%d;%d;%dm",
               $fore?38:48, $c->r, $c->g, $c->b ));
         default:
            throw $this->unColor();
      }
   }

   protected function unKind() {
      return new EVy("Unknown terminal kind: $this->kind");
   }

   protected function unColor() {
      return new EVy("Unknown terminal color: $this->color");
   }
}
