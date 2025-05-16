<?php

namespace vy;

/// bitkép
class Bitmap {

   const
      HEIGHT = "height",
      IMPL = "impl",
      PIXEL = "pixel",
      WIDTH = "width";

   static function fromImpl( $impl ) {
      $ret = new Bitmap();
      $ret->impl = $impl;
      VypGui::gui()->createBitmap($ret);
      return $ret;
   }

   protected $width;
   protected $height;
   protected $pixel;
   protected $impl;

   function __destruct() {
      VypGui::gui()->destroyBitmap($this);
   }

   function __get($fld) {
      switch ($fld) {
         case self::HEIGHT: return $this->height;
         case self::IMPL: return $this->impl;
         case self::PIXEL: return $this->pixel;
         case self::WIDTH: return $this->width;
         default: throw Tools::unFld($fld);
      }
   }

   function setFields( $width, $height, $pixel ) {
      $this->width = $width;
      $this->height = $height;
      $this->pixel = $pixel;
   }

}
