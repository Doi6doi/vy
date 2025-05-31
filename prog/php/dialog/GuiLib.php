<?php

namespace vy;

abstract class GuiLib {

   static function load() {
      return new ReadLine();
//      return new Console();
//      return new Sdl();
   }

   /// gui rendszer futtatása
   abstract function run();

   /// bitmap készítése
   abstract function createBitmap(Bitmap $b);

   /// új font készítése
   abstract function createFont(Font $f);

   /// új ablak készítése
   abstract function createWindow(Window $w);

   /// alapértelmezett font
   abstract function defaultFont();

   /// bitmap felszámolása
   abstract function destroyBitmap(Bitmap $b);

   /// font felszámolása
   abstract function destroyFont(Font $f);

   /// ablak felszámolása
   abstract function destroyWindow(Window $w);

   /// bitmap kirajzolása
   abstract function drawBitmap( Canvas $c, Point $at, 
      Bitmap $b );

   /// szöveg kiírása
   abstract function drawText( Canvas  $c, Point $at, 
      string $txt, Font $f, Pen $l );

   /// ablak kiterjedésének lekérdezése
   abstract function getWindowBounds( Window $w ): Rect;
   
   /// ablakkerekt méretei
   abstract function getWindowBorders( Window $w ): Borders;

   /// rajzolt elemek megjelenítése
   abstract function refreshWindow( Window $w );

   /// ablak fejlécének változtatása
   abstract function setWindowTitle( Window $w, $s );

   /// ablak megjelenítése, vagy eltüntetése
   abstract function setWindowVisible( Window $w, $on );

   /// szöveg kirajzolási mérete
   abstract function textSize( string $text, Font $f );

   /// font változás átvezetése
   abstract function updateFont( Font $f, string $field );
   

}
