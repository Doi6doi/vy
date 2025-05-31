<?php

namespace vy;

/// readline kimenet (print + gets)
class ReadLine
   extends GuiLib
{

   /// aktuális ablak
   protected $window;
   /// fókuszált elem
   protected $focused;
   /// alap font
   protected $defaultFont;

   function run() {
      while ( $this->focused ) {
         $this->draw( $this->focused );
         $this->readProcess();
      }
   }

   /// beírt sor feldolgozása
   function readProcess() {
      $r = $this->read();
      $f = $this->focused;
      if ($f instanceof Group) {
         if ( $this->isBack($r) )
            $this->focused = $f->parent;
         else if ( $this->isInt($r) )
            $this->select($r);
         else
            $this->expect("[0-9]*");
      } else
         $this->focused = $f->parent;
   }

   function createWindow(Window $w) { 
      $this->setWindowVisible($w,true);
   }

   function destroyWindow(Window $w) {
   }

   function createBitmap(Bitmap $b) { }

   function destroyBitmap(Bitmap $b) { }

   function createFont(Font $f) { }

   function updateFont(Font $f, string $field ) { }

   function destroyFont(Font $f) { }

   function defaultFont() { 
      if ( ! $this->defaultFont )
         $this->defaultFont = Font::byFile( "", 1 );
      return $this->defaultFont;
   }

   function drawRect( Canvas $c, Rect $r, Pen $l ) { }

   function fillRect( Canvas $c, Rect $r, Fill $l ) { }

   function textSize( string $text, Font $f ) {
      return new Point( Tools::uLen($text), 1 );
   }

   function drawText( Canvas $c, Point $at, 
      string $text, Font $f, Pen $l ) 
   { }
   
   function drawBitmap( Canvas $c, Point $at, Bitmap $b ) { }

   function refreshWindow( Window $w ) { }

   function getWindowBounds( Window $w ): Rect { 
      return new Rect(0,0,0,0);
   }

   function getWindowBorders( Window $w ) : Borders {
      return new Borders(0,0,0,0); 
   }

   function setWindowVisible( Window $w, $on ) {
      if ($on) {
         $this->window = $this->focused = $w;
      } else if ( $w == $this->window ) {
         $this->window = $this->focused = null;
      }
   }

   function setWindowTitle( Window $w, $s ) { }

   function draw( $v ) {
      if ( ! $this->visible($v)) return;
      if ($v instanceof Group) {
         $n = 0;
         foreach ($v->items() as $i) {
            if ($this->visible($i)) {
               if ( $this->choosable($i)) {
                  print( sprintf( "%d: %s\n", ++$n, $this->text($i) ));
               } else {
                  print( sprintf( "%s\n", $this->text($i)));
               }
            }
         }
      } else
         throw new EVy("Cannot print ".get_class($v));
   }

   function read() {
      print("? ");
      return fgets(STDIN);
   }

   /// visszalépés
   function isBack($s) {
      $s = trim($s);
      return "" == $s;
   }

   /// egész szám
   function isInt($s) {
      return is_numeric( trim($s));
   }

   /// vezérlő szövege
   protected function text( $v ) {
      return $v->text;
   }

   /// opció kiválasztása
   protected function select($i) {
      $i = (int)trim($i);
      $j = 0;
      foreach ($this->focused->items() as $v) {
         if ( $this->visible($v) && $this->choosable($v)) {
            ++ $j;
            if ( $i == $j )
               return $this->selectView($v);
         }
      }
      $this->expect( "1-$j" );
   }
         
   /// Vezérlőelem kiválasztása
   protected function selectView($v) {
      if ($v instanceof Button) 
         return $v->handle( Event::click($v) );
      else
         throw new EVy("Cannot select $c");
   }
         
   /// elvárt szöveg
   protected function expect($s) {
      print("$s ");
      $this->readProcess();
   }

   /// látható elem
   protected function visible($v) {
      return in_array( get_class($v), 
         [Text::class, Button::class, Window::class] );
   }
   
   /// választható elem
   protected function choosable($v) {
      return in_array( get_class($v), 
         [Button::class] );
   }      

}

