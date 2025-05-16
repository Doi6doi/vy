<?php

namespace vy;

class Sdl
   extends GuiLib
{

   const
      TICK = 0.05;

   const
      SDL_INIT_EVENTS = 0x4000,
      SDL_INIT_VIDEO  = 0x20;

   const
      SDL_QUIT = 0x100,
      SDL_WINDOWEVENT = 0x200,
      SDL_MOUSEMOTION = 0x400,
      SDL_MOUSEBUTTONDOWN = 0x401, 
      SDL_MOUSEBUTTONUP = 0x402,
      SDL_MOUSEWHEEL = 0x403,
      SDL_FINGERDOWN = 0x700,
      SDL_FINGERUP = 0x701,
      SDL_FINGERMOTION = 0x702;

   const
      SDL_WINDOWEVENT_EXPOSED = 3,
      SDL_WINDOWEVENT_RESIZED = 5;

   const
      SDL_WINDOW_RESIZABLE = 0x20,
      SDL_WINDOWPOS_CENTERED = 0x2FFF0000;

   const
      NINTS = 4,
      NRECTS = 3;

   /// sdl könyvtár
   protected $ffi;
   /// sdl_ttf könyvtár
   protected $fft;
   /// be kell-e zárni a programot
   protected $quit;
   /// sdl esemény
   protected $event;
   /// utolsó gomb esemény
   protected $lastButton;
   /// nyilvántartott ablakok
   protected $windows;
   /// C int változók
   protected $ints;
   /// mutató ints-re
   protected $pints;
   /// SDL_Rect változó
   protected $rects;
   /// mutató rects-re
   protected $prects;
   /// SDL_Color változó
   protected $color;
   /// alap font
   protected $defaultFont;

   function __construct() {
      $this->init();
      $this->windows = [];
   }

   function __destruct() {
      $this->done();
   }

   function window( $id, $part) {
      if ( ! $r = Tools::g( $this->windows, $id ))
         throw new EVy("Window not found: $id");
      return $r[$part];
   }

   function run() {
      if ( $this->hasVisibleWindows() ) {
         while ( ! $this->quit ) {
            $this->processEvent();
         }
      }
   }

   function sdlLib() { return "libSDL2.so"; }

   function ttfLib() { return "libSDL2_ttf.so"; }

   function hasVisibleWindows() {
      foreach ($this->windows as $w)
         if ( $w[0]->visible ) return true;
      return false;
   }

   /// esemény olvasása és feldolgozása
   function processEvent() {
      if ( $this->ffi->SDL_PollEvent( $this->a( $this->event ) )) {
         if ( $e = $this->createEvent() )
            $this->handle($e);
      } else {
         if ( ! $this->refresh() )
            usleep( self::TICK * 1000000 );
      }
   }

   /// egy esemény feldolgozása
   function handle( Event $e ) {
      switch ( $e->kind ) {
         case Event::QUIT: 
            $this->quit = true; break;
         case Event::BUTTON:
         case Event::MOVE:
         case Event::SCROLL:
         case Event::RESIZE:
            return $this->handleView( $e );
         default:
            throw $e->unKind();
      }
   }

   function createWindow(Window $w) {
      $f = $this->ffi;
      $this->check( $f->SDL_GetDisplayBounds( 0, $this->prects ));
      $r = $this->rects[0];
      $this->check( $sw = $f->SDL_CreateWindow( "",
         self::SDL_WINDOWPOS_CENTERED, self::SDL_WINDOWPOS_CENTERED,
         $r->w*2/3, $r->h*2/3, self::SDL_WINDOW_RESIZABLE ));
      if ( ! $id = $f->SDL_GetWindowID($sw))
         throw new EVy( $f->SDL_GetError() );
      $this->check( $sr = $f->SDL_CreateRenderer( $sw, -1, 0 ));
      $this->windows[$id] = [$w,$sw,$sr];
      $w->impl = $id;
   }

   function destroyWindow(Window $w) {
      if ( $this->quit ) return;
      $f = $this->ffi;
      $f->SDL_DestroyRenderer($this->window($w->impl,2));
      $f->SDL_DestroyWindow($this->window($w->impl,1));
   }

   function createBitmap(Bitmap $b) {
      if ($tex = $b->impl) {
         $f = $this->ffi;
         $this->check( $f->SDL_QueryTexture( $tex, null, null,
            $this->pints+0, $this->pints+1 ));
         $b->setFields( $this->ints[0], $this->ints[1], Pixel::CUSTOM );
      } else {
         throw new EVy("nyf");
      }
   }

   function destroyBitmap(Bitmap $b) {
      if ( ! $this->quit )
         $this->ffi->SDL_DestroyTexture($b->impl);
   }

   function createFont(Font $f) {
      $t = $this->fft;
      $this->check( $ret = $t->TTF_OpenFont( $f->fileName, $f->size ));
      $f->impl = $ret;
   }

   function updateFont(Font $f, string $field ) {
      $t = $this->fft;
      switch ($field) {
         case Font::SIZE: 
            $this->check( $t->TTF_SetFontSize( $f->impl, $f->size ));
         break;
         case Font::FILENAME:
            $t->CloseFont( $f->impl );
            $this->createFont($f);
         break;
      }
   }

   function destroyFont(Font $f) {
      if ( ! $this->quit )
         $this->fft->TTF_CloseFont($f->impl);
   }

   function defaultFont() { 
      if ( ! $this->defaultFont )
         $this->defaultFont = Font::byFile( __DIR__."/dvsm.ttf", 12 );
      return $this->defaultFont;
   }

   function drawRect( Canvas $c, Rect $r, Pen $l ) {
      $f = $this->ffi;
      $ren = $this->window( $c->window()->impl, 2 );
      $this->setColor( $ren, $l->color );
      $r = Rect::move( $r, $c->offset() );
      $this->fromRect( $r );
      while ($this->crop($c,$i))
         $this->check( $f->SDL_RenderDrawRect( $ren, $this->prects ));
   }

   function fillRect( Canvas $c, Rect $r, Fill $l ) {
      $f = $this->ffi;
      $ren = $this->window($c->window()->impl,2);
      $this->setColor( $ren, $l->color );
      $r = Rect::move( $r, $c->offset() );
      $this->fromRect( $r );
      while ( $this->crop($c,$i) )
         $this->check( $f->SDL_RenderFillRect( $ren, $this->prects ));
   }

   function textSize( string $text, Font $f ) {
      $t = $this->fft;
      $this->check( $t->TTF_SizeUTF8( $f->impl, $text,
         $this->pints+0, $this->pints+1 ));
//      $fa = $t->TTF_FontAscent( $f->impl );
      return new Point( $this->ints[0], $this->ints[1] );
//      return new Point( $this->ints[0], 0 );
   }

   function drawText( Canvas $c, Point $at, 
      string $text, Font $f, Pen $l ) 
   {
      if ( "" === trim($text)) return;
      $sr = $this->window( $c->window()->impl, 2 );
      $b = $this->textBitmap( $sr, $text, $f, $l );
      $this->drawBitmap( $c, $at, $b );
   }
   
   /// bmp mentése
   function saveSurface( $sur, $fname ) {
      $f = $this->ffi;
      $this->check( $ops = $f->SDL_RWFromFile( $fname, "w" ));
      $this->check( $f->SDL_SaveBMP_RW( $sur, $ops, 1 ));
   }
   
   function textBitmap( $ren, string $text, Font $f, Pen $l ) {
      $this->fromColor( $l->color );
      $fi = $this->ffi;
      $t = $this->fft;
      $tc = $t->cast("SDL_Color",$this->color);
      if ( $f->fast )
         $ts = $t->TTF_RenderUTF8_Solid( $f->impl, $text, $tc );
         else $ts = $t->TTF_RenderUTF8_Blended( $f->impl, $text, $tc );
      $this->check($ts);
      $sur = $fi->cast("SDL_Surface *",$ts);
      $tex = $this->check( $fi->SDL_CreateTextureFromSurface( $ren, $sur ));
// $this->saveSurface( $sur, "b.bmp");
      $fi->SDL_FreeSurface( $sur );            
      return Bitmap::fromImpl( $tex );
   }

   function drawBitmap( Canvas $c, Point $at, Bitmap $b ) {
      $f = $this->ffi;
      $ren = $this->window($c->window()->impl,2);
      $d = new Rect( $at->x,$at->y,$b->width,$b->height );
      $d = Rect::move( $d, $c->offset() );
      $this->fromRect( $d );
      while ( $this->crop($c,$i) )
         $this->check( $f->SDL_RenderCopy( $ren, $b->impl, null, $this->prects ));
   }

   function refreshWindow( Window $w ) { 
      $sr = $this->window($w->impl, 2);
      $this->check( $this->ffi->SDL_RenderPresent($sr) );
   }

   function refresh() {
      $ret = false;
      foreach ($this->windows as $w) {
         if ( $w[0]->refresh() )
            $ret = true;
      }
      return $ret;
   }

   function getWindowBounds( Window $w ): Rect {
      $f = $this->ffi;
      $i = $this->ints;
      $p = $this->pints;
      $sw = $this->window($w->impl,1);
      $f->SDL_GetWindowPosition( $sw, $p+0, $p+1 );
      $f->SDL_GetWindowSize( $sw, $p+2, $p+3 );
      return new Rect( $i[0], $i[1], $i[2], $i[3] );
   }

   function getWindowBorders( Window $w ) : Borders {
      $f = $this->ffi;
      $i = $this->ints;
      $p = $this->pints;
      $sw = $this->window($w->impl,1);
      $this->check( $f->SDL_GetWindowBordersSize( $sw, $p+0, $p+1, $p+2, $p+3 ));
      return new Borders( $i[1], $i[0], $i[3], $i[2] );
   }

   function setWindowVisible( Window $w, $on ) {
      $sw = $this->window($w->impl,1);
      if ( $on )
         $this->ffi->SDL_ShowWindow($sw);
         else $this->ffi->SDL_HideWindow($sw);
   }

   function setWindowTitle( Window $w, $s ) {
      $sw = $this->window($w->impl,1);
      $this->ffi->SDL_SetWindowTitle( $sw, $s );
   }

   protected function a( $x ) {
      return \FFI::addr( $x );
   }

   protected function check( $x ) {
      if ( null === $x || is_numeric($x) && 0 != $x ) {
         $s = $this->ffi->SDL_GetError();
         throw new EVy( $s );
      }
      return $x;
   }

   /// view esemény feldolgozása
   protected function handleView( Event $e ) {
      $w = $e->view;
      if ( $e->loc ) {
         $v = $w->viewAt( $e->loc );
         if ( $w != $v ) {
            $e->loc = $v->coords( $e->loc, Coord::FROMWINDOW );
            $e->view = $v;
         }
         $w->hovered = $v;
      }
      $e->view->handle( $e );
      if ( Event::BUTTON == $e->kind )
         $this->handleButton( $e );
   }

   /// gomb le és fel esetén kattintás
   protected function handleButton( Event $e ) {
      if ( $this->lastButton && $this->lastButton->down
         && ! $e->down && $this->lastButton->view = $e->view )
      {
         $c = clone $e;
         $c->kind = Event::CLICK;
         $c->view->handle( $c );
      }
      $this->lastButton = $e;
   }

   protected function init() {
      $sh = Tools::loadFile( __DIR__."/sdl.h" );
      $f = $this->ffi = \FFI::cdef( $sh, $this->sdlLib() );
      $th = Tools::loadFile( __DIR__."/sdlttf.h" );
      $this->fft = \FFI::cdef( $th, $this->ttfLib() );
      $this->windows = [];
      $this->check( $f->SDL_Init(
         self::SDL_INIT_VIDEO | self::SDL_INIT_EVENTS ));
      $this->check( $this->fft->TTF_Init() );
      $this->event = $f->new( "SDL_Event" );
      $this->ints = $f->new("int[".self::NINTS."]");
      $this->pints = $f->cast("int *", $this->ints );
      $this->rects = $f->new("SDL_Rect[".self::NRECTS."]");
      $this->prects = $f->cast("SDL_Rect *", $this->rects );
      $this->color = $f->new("SDL_Color");
   }

   protected function done() {
      $this->fft->TTF_Quit();
      $this->ffi->SDL_Quit();
   }

   /// viewport beállítása a canvas rects egy részére
   protected function crop( Canvas $c, & $i ) {
      $i = intval($i);
      $f = $this->ffi;
      $rs = $c->crop()->rects();
      $sr = $this->window( $c->window()->impl, 2 );
      if ( count($rs) <= $i) {
         $this->check( $f->SDL_RenderSetClipRect( $sr, null ));
         return false;
      }
      $k = self::NRECTS-1;
      $this->fromRect( $rs[$i], $k );
      $this->check( $f->SDL_RenderSetClipRect( $sr, $this->prects+$k ));
      ++$i;
      return true;
   }

   /// esemény készítése sdl esemény alapján
   protected function createEvent() {
      switch ( $this->event->type ) {
         case self::SDL_MOUSEMOTION: 
         case self::SDL_MOUSEBUTTONDOWN: 
         case self::SDL_MOUSEBUTTONUP: 
         case self::SDL_MOUSEWHEEL: 
         case self::SDL_FINGERDOWN:
         case self::SDL_FINGERUP:
         case self::SDL_FINGERMOTION:
            return $this->createPointerEvent();
         case self::SDL_QUIT: return new Event( Event::QUIT );
         case self::SDL_WINDOWEVENT: return $this->createWindowEvent();
         default: return null;
      }
   }

   /// esemény készítése sdl esemény alapján
   protected function createWindowEvent() {
      $ew = $this->event->window;
      $ret = new Event();
      switch ( $ew->event ) {
         case self::SDL_WINDOWEVENT_EXPOSED:
            $this->refreshWindow( $this->window( $ew->windowID, 0 ) );
         return null;
         case self::SDL_WINDOWEVENT_RESIZED: $ret->kind = Event::RESIZE; break;
         default: return null;
      }
      $ret->view = $this->window( $ew->windowID, 0 );
      return $ret;
   }

   /// esemény készítése mutató esemény alapján
   protected function createPointerEvent() {
      $e = $this->event;
      $ret = new Event();
      switch ( $t = $this->event->type ) {
         case self::SDL_MOUSEMOTION: 
            $ew = $e->motion;
            $ret->kind = Event::MOVE;
         break;
         case self::SDL_MOUSEBUTTONDOWN:
         case self::SDL_MOUSEBUTTONUP:
            $ew = $e->button;
            $ret->kind = Event::BUTTON;
            $ret->index = $ew->button;
            $ret->down = (self::SDL_MOUSEBUTTONDOWN == $t);
         break;
         case self::SDL_MOUSEWHEEL: 
            $ew = $e->wheel;
            $ret->kind = Event::SCROLL;
         break;
         case self::SDL_FINGERDOWN:
         case self::SDL_FINGERUP:
            $ew = $e->tfinger;
            $ret->kind = Event::BUTTON;
            $ret->index = $ew->fingerId;
            $ret->down = (self::SDL_FINGERDOWN == $t);
         break;
         case self::SDL_FINGERMOTION:
            $ew = $e->tfinger;
            $ret->kind = Event::MOVE;
         break;
         default: throw $e->unKind();
      }
      $ret->view = $this->window( $ew->windowID, 0 );
      switch ($t) {
         case self::SDL_FINGERDOWN:
         case self::SDL_FINGERMOTION:
         case self::SDL_FINGERUP:
            $c = $ret->view->client;
            $ret->loc = new Point( $ew->x * $c->width, $ew->y * $c->height );
         break;
         default:
            $ret->loc = new Point( $ew->x, $ew->y );
      }
      return $ret;
   }

   protected function fromRect( Rect $r, $i=0 ) {
      $q = $this->rects[$i];
      $q->x = $r->left;
      $q->y = $r->top;
      $q->w = $r->width;
      $q->h = $r->height;
   }

   protected function fromColor( Color $c ) {
      $q = $this->color;
      $q->r = $c->r;
      $q->g = $c->g;
      $q->b = $c->b;
      $q->a = $c->a;
   }

   protected function setColor( $ren, Color $c ) {
      $f = $this->ffi;
      $this->check( $f->SDL_SetRenderDrawColor( $ren, 
         $c->r, $c->g, $c->b, $c->a ));
   }

}

