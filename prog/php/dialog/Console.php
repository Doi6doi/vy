<?php

namespace vy;

/// konzol kimenet (ncurses, vagy windows)
class Console
   extends GuiLib
{

   const
      TICK = 0.05;

   /// konzol objektum
   protected $con;
   /// be kell-e zárni a programot
   protected $quit;
   /// nyilvántartott ablakok
   protected $windows;
   /// látható ablak
   protected $foreWindow;
   /// utolsó gomb esemény
   protected $lastButton;
   /// alap font
   protected $defaultFont;

   function __construct() {
      $this->init();
      $this->windows = [];
   }

   function __destruct() {
      $this->done();
   }

   function run() {
      while ( ! $this->quit && $this->foreWindow ) {
         $this->foreWindow->refresh();
         $this->processEvent();
      }
   }

   /// esemény olvasása és feldolgozása
   function processEvent() {
      $e = $this->con->pollEvent();
      $this->handle($e);
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
         case Event::KEY:
         case Event::TEXT:
            return $this->handleView( $e );
         default:
            throw $e->unKind();
      }
   }

   function createWindow(Window $w) {
      $this->windows [] = $w;
      $this->updateForeWindow();
   }

   function destroyWindow(Window $w) {
      if ( false !== $i = array_search( $w, $this->windows ))
         array_splice( $this->windows, $i, 1 );
      $this->updateForeWindow();
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

   function drawRect( Canvas $c, Rect $r, Pen $l ) {
      $this->con->setFore( $l->color );
      $r = Rect::move( $r, $c->offset() );
      while ($this->crop($c,$i))
         $this->con->drawRect( $r );
   }

   function fillRect( Canvas $c, Rect $r, Fill $l ) {
      $this->con->setBack( $l->color );
      $r = Rect::move( $r, $c->offset() );
      while ($this->crop($c,$i))
         $this->con->fillRect( $r );
   }

   function textSize( string $text, Font $f ) {
      return new Point( Tools::uLen($text), 1 );
   }

   function drawText( Canvas $c, Point $at, 
      string $text, Font $f, Pen $l ) 
   {
      $this->con->setFore( $l->color );
      $at = Point::add($at,$c->offset());
      while ($this->crop($c,$i))
         $this->con->text( $at, $text );
   }
   
   function drawBitmap( Canvas $c, Point $at, Bitmap $b ) { }

   function refreshWindow( Window $w ) { 
      $this->con->refresh();
   }

   function getWindowBounds( Window $w ): Rect {
      $s = $this->con->size();
      return new Rect(0,0,$s->x,$s->y);
   }

   function getWindowBorders( Window $w ) : Borders {
      return new Borders(0,0,0,0); 
   }

   function setWindowVisible( Window $w, $on ) {
      $this->updateForeWindow();
   }

   function setWindowTitle( Window $w, $s ) {
      if ( $w == $this->foreWindow )
         $this->con->setTitle( $s );
   }

   protected function updateForeWindow() {
      if ( ! in_array( $this->foreWindow, $this->windows ))
         $this->foreWindow = null;
      if ( $this->foreWindow ) return;
      for ($i=count($this->windows)-1; 0 <=$i; --$i ) {
         $w = $this->windows[$i];
         if ( $w->visible ) {
            $this->foreWindow = $w;
            $w->refresh();
            return;
         }
      }
      $this->con->clear();
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
      } else if ( in_array( $e->kind, [Event::KEY, Event::TEXT] )) {
         if ( $w->focused )
            $e->view = $w->focused;
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
      $this->windows = [];
      switch ( $s = Tools::system() ) {
         case "Linux": $this->con = new Curses(); break;
         case "Windows": $this->con = new WinConsole(); break;
         default: throw new EVy("Cannot uses console in $s");
      }
      $this->con->init();
   }

   protected function done() {
      $this->con->done();
   }

   /// viewport beállítása a canvas rects egy részére
   protected function crop( Canvas $c, & $i ) {
      if ( $c->window() != $this->foreWindow ) 
         return false;
      $i = intval($i);
      $rs = $c->crop()->rects();
      if ( count($rs) <= $i) {
         $this->con->crop(null);
         return false;
      }
      $this->con->crop( $rs[$i] );
      ++$i;
      return true;
   }

}

