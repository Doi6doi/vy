<?php

namespace vy;

/// ncurses kimenet
class Curses
   implements IConsole
{

   const
      LIBNAME = "libncursesw.so.6",
      NWINTS = 1;

   /// c könyvtár
   protected $ffi;
   /// fő ablak
   protected $sw;
   /// tárolt háttérszínek
   protected $bbuf;
   /// háttérszín
   protected $back;
   /// előtérszín
   protected $fore;
   /// crop terület
   protected $crop;
   /// van-e fejléc sor
   protected $head;
   /// wint-ek
   protected $wints;
   /// és cím
   protected $pwints;

   function __destruct() {
      $this->done();
   }

   function init() {
      $this->bbuf = new ColorRects();
      $this->back = Color::parse("#000");
      $this->fore = Color::parse("#fff");
      $h = Tools::loadFile( __DIR__."/curses.h" );
      $f = $this->ffi = \FFI::cdef( $h, self::LIBNAME );
         $this->wints = $f->new("wint_t[".self::NWINTS."]");
      $this->pwints = $f->cast("wint_t *", $this->wints );
      if ( ! $this->sw = $f->initscr() )
         throw new EVy("Could not initialize ncurses");
      $this->check( $f->cbreak(), "cbreak" );
      $this->check( $f->noecho(), "noecho" );
      $this->check( $f->keypad( $this->sw, true ), "keypad" );
      $this->head = ! Terminal::ins()->can( Terminal::TITLE );
   }

   function done() {
      if ( ! $this->sw ) return;
//      $this->check( $this->ffi->endwin(), "done" );
      $this->sw = null;
   }
   
   function clear() {
      $this->check( $this->ffi->clear(), "clear" );
      $this->refresh();
   }
   
   function size() {
      $x = $this->ffi->getmaxx( $this->sw );
      $y = $this->ffi->getmaxy( $this->sw );
      if ( $this->head )
         -- $y;
      return new Point( $x, $y );
   }
   
   /// cím beállítása
   function setTitle($x) {
      if ($this->head)
         $this->drawHead($x);
         else Terminal::ins()->setTitle($x);
   }
   
   function pollEvent() {
      $ch = $this->ffi->get_wch($this->pwints);
      switch ($ch) {
         case 0: return $this->keyEvent($this->wints[0]);
         default: throw new EVy("Unknown curses event: $ch");
      }
   }
   
   function fillRect( Rect $r ) {
      if ( ! $r = Rect::intersect( $r, $this->crop ))
         return;
      $this->bbuf->minus($r);
      $t = Terminal::ins();
      $t->setBack($this->back);
      $txt = str_repeat( " ",$r->width );
      for ($y=0; $y<$r->height; ++$y) {
         $this->move( $r->top + $y, $r->left );
         $t->write( $txt );
      }
      $this->bbuf->add($r,$this->back); 
   }
   
   function drawRect( Rect $r ) {
      if ( $r->empty() ) return;
      $w = $r->width;
      $h = $r->height;
      if ( 1 == $h ) {
         $this->drawHorz( $r->left, $r->top, $w,"╞═╡");
      } else {
         $this->drawHorz( $r->left, $r->top, $w, "┌─┐");
         $this->drawHorz( $r->left, $r->bottom()-1, $w, "└─┘");
      }
      if ( 1 == $w ) {
         $this->drawVert( $r->left, $r->top, $h, "╥║╨");
      } else {
         $this->drawVert( $r->left, $r->top+1, $h-2, "║║║");
         $this->drawVert( $r->right()-1, $r->top+1, $h-2, "║║║");
      }
   }
   
   function setBack( Color $c ) {
      $this->back = $c;
   }
   
   function setFore( Color $c ) {
      $this->fore = $c;
   }

   function crop( $r ) {
      $s = $this->size();
      $s = new Rect(0,0,$s->x,$s->y);
      if ( null === $r )
         $this->crop = $s;
         else $this->crop = Rect::intersect( $r, $s );
   }
   
   function refresh() {
      $this->check( $this->ffi->refresh(), "refresh" );
   }

   /// szöveg kiírása
   function text( Point $p, $txt ) {
      $c = $this->crop;
      if ( ! Tools::contains( $c->top, $c->height, $p->y ) ) return;
      $t = Terminal::ins();
      $chs = Tools::uSplit( $txt );
      $last = null;
      for ($i=0; $i<count($chs); ++$i) {
         $xi = $p->x+$i;
         if ( Tools::contains( $c->left, $c->width, $xi ) ) {
            if ( ! $last )
               $this->move( $xi, $p->y );
            $l = $this->bbuf->get( $xi, $p->y );
            if ( $last != $l ) {
               $t->setBack( $l );
               $last = $c;
            }
            $t->write( $chs[$i] );
         } else if ( $last )
            return;
      }
   }

   /// vízszintes vonal rajzolása
   protected function drawHorz( $x, $y, $n, $chs ) {
      $chs = Tools::uSplit( $chs );
      switch ( $n ) {
         case 0: return;
         case 1: $txt = $chs[1]; break;
         default: $txt = $chs[0].str_repeat($chs[1],$n-2).$chs[2];
      }
      $this->text( new Point( $x, $y ), $txt );
   }

   /// függőleges vonal rajzolása
   protected function drawVert( $x, $y, $n, $chs ) {
      $chs = Tools::uSplit( $chs );
      $p = new Point($x,$y);
      switch ( $n ) {
         case 0: return;
         case 1: $this->text($p,$chs[1]); break;
         default: 
            for ($i=0; $i<$n; ++$i) {
               $ch = (0==$i?$chs[0]:($n-1==$i?$chs[2]:$chs[1]));
               $this->text($p,$ch);
               ++$p->y;
            }
      }
   }

   protected function move( $x, $y ) {
      if ( $this->head )
         ++ $y;
      Terminal::ins()->move( $x+1, $y+1 );
   }
   
   protected function check($x, $meth) {
      if ( 0 != $x )
         throw new EVy(sprintf("Curses.%s failed: %d", $meth, $x ));
   }


}

