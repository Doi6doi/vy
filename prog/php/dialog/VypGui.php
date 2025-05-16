<?php

namespace vy;

class VypGui {

   protected static $ins;

   static function run() {
      self::gui()->run();
   }

   static function ins() {
      if ( ! self::$ins )
         self::$ins = new VypGui();
      return self::$ins;
   }

   static function gui() {
      return self::ins()->gui;
   }

   protected $gui;

   protected function __construct() {
      $this->gui = GuiLib::load();
   }

}
