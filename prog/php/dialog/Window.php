<?php

namespace vy;

class Window
   extends Group
{

   const
      TITLE = "title",
      WINDOW = "window";

   const
      ALL = [self::TITLE];

   protected $title;
   protected $dirty;
   protected $canvas;
   protected $borders;
   protected $modalResult;

   function __construct() {
      $g = VypGui::gui();
      $g->createWindow($this);
      $this->dirty = new Rects();
      parent::__construct();
      $this->bounds = $g->getWindowBounds( $this );
      $this->borders = $g->getWindowBorders( $this );
      $this->canvas = new Canvas($this);
      $this->skin = Skin::default();
      $this->theme = Theme::default();
      $this->font = $g->defaultFont();
      $this->invalidate();
   }

   function __get($fld) {
      switch ($fld) {
         case self::TITLE: return $this->title;
         default: return parent::__get($fld);
      }
   }

   function __set($fld,$x) {
      switch ($fld) {
         case self::TITLE: return $this->setTitle($x);
         case self::FOCUSED: return $this->setFocused($x);
         case self::HOVERED: return $this->setHovered($x);
         default: return parent::__set($fld,$x);
      }
   }

   function build( $d ) {
      parent::build( $d );
      Tools::buildProps( $this, $d, self::ALL );
   }

   /// ablak tartalmának frissítése
   function refresh() {
      $d = $this->dirty;
      if ( $ret = ! $d->empty() ) {
         $this->canvas->cropRects( $d );
         $this->paint($this->canvas);
         $d->clear();
         $g = VypGui::gui()->refreshWindow( $this );
      }
      $this->canvas->initCrop();
      return $ret;
   }

   function invalidate( $rect = null ) {
      if ( ! $this->visible ) return;
      if ( ! $rect ) {
         $c = $this->client;
         $rect = new Rect(0,0, $c->width, $c->height );
      }
      $this->dirty->unionRect( $rect );
   }

   function shown() { return $this->visible; }

   function window() { return $this; }

   function coords( Point $p, $mode ) {
      switch ($mode) {
         case Coord::FROMWINDOW:
         case Coord::TOWINDOW:
            return $p;
         case Coord::FROMPARENT:
         case Coord::TOPARENT:
            $this->parent();
         default:
            return parent::coords($p,$mode);
      }
   }

   /// következő elem fókuszálása
   function focusNext($forw) {
      if ( ! $f = $this->focused )
         if ( ! $f = Tools::g( $this->items, 0 ))
            return;
      $g = $f;
      do {
         $g = $g->next( $forw, true );
      } while ( $f != $g && ! $g->focusable() );
      $this->setFocused($g);
   }

   /// elrejtés, vagy felfedés
   protected function setVisible($x) {
      $this->visible = $x = !! $x;
      VypGui::gui()->setWindowVisible( $this, $x );
      $this->invalidate();
   }

   protected function resize() {
      $this->bounds = VypGui::gui()->getWindowBounds( $this );
      $this->canvas->initCrop();
      return parent::resize();
   }

   protected function next($forw,$in) {
      if ( ! $c = count($this->items) ) return null;
      return $forw ? $this->items[0] : $this->items[$c-1];
   }

   protected function setTitle($x) {
      VypGui::gui()->setWindowTitle( $this, $x );
      $this->title = $x;
   }

   protected function setHovered( $x ) {
      if ( $this == $x ) $x = null;
      if ( $x == $this->hovered ) return;
      if ( $this->hovered )
         $this->hovered->setHovered(false);
      if ( $this->hovered = $x )
         $x->setHovered(true);
   }

   protected function setFocused( $x ) {
      if ( $this == $x ) $x = null;
      if ( $x == $this->focused ) return;
      if ( $x && ! $x->focusable() ) return;
      if ( $this->focused )
         $this->focused->setFocused(false);
      if ( $this->focused = $x )
         $x->setFocused(true);
   }

   protected function clearDirty() {
      $this->dirty->unionRect(
         new Rect(0,0,$this->clientWidth,$this->clientHeight));
   }

}
