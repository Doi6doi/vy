<?php

namespace vy;

class Dialog {

   const
      KIND = "kind";

   static function build( $d ) {
      if ( ! Tools::isAssoc($d))
         throw new EVy("Hash expected");
      $kind = Tools::g( $d, self::KIND );
      $ret = self::create( $kind );
      $ret->build( $d );
      return $ret;
   }

   static function create( $kind ) {
      switch ($kind) {
         case Button::BUTTON: return new Button();
         case Text::TEXT: return new Text();
         case Window::WINDOW: return new Window();
         default: throw new EVy("Unknown kind: $kind");
      }
   }

}
