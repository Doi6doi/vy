<?php

namespace vy;

class View {

   const
       BOUNDS = "bounds",
       HOVERED = "hovered",
       IMPL = "impl",
       PARENT = "parent",
       PREFERRED = "preferred",
       THEME = "theme",
       SKIN = "skin",
       VISIBLE = "visible";

   protected $parent;
   protected $visible;
   protected $impl;
   protected $bounds;
   protected $theme;
   protected $skin;
   protected $handlers;
   protected $hovered;

   function __construct() {
      $this->visible = true;
      $this->handlers = [];
      $this->bounds = new Rect(0,0,0,0);
      $this->theme = Theme::byOwner( $this );
      $this->skin = Skin::byOwner( $this );
   }

   function __get($fld) {
      switch ($fld) {
         case self::BOUNDS: return $this->bounds;
         case self::HOVERED: return $this->hovered;
         case self::IMPL: return $this->impl;
         case self::PARENT: return $this->parent;
         case self::PREFERRED: return $this->preferred();
         case self::THEME: return $this->theme;
         case self::SKIN: return $this->skin;
         case self::VISIBLE: return $this->visible;
         case Coord::BOTTOM:
         case Coord::HEIGHT:
         case Coord::LEFT:
         case Coord::RIGHT:
         case Coord::TOP:
         case Coord::WIDTH:
            return $this->coord($fld);
         default: throw Tools::unFld($fld);
      }
   }

   function __set($fld,$x) {
      switch ($fld) {
         case self::BOUNDS: return $this->setBounds($x);
         case self::IMPL: return $this->impl = $x;
         case self::VISIBLE: return $this->setVisible($x);
         default: throw Tools::unFld($fld);
      }
   }

   function build( $d ) {
      $this->theme->build( Tools::g( $d, self::THEME ));
      $this->skin->build( Tools::g( $d, self::SKIN ));
      $this->bounds->build( Tools::g( $d, self::BOUNDS ));
      Tools::buildProps($this, $d, [self::VISIBLE] );
   }

   /// eseménykezelő beállítása
   function on(string $eventKind, $h) {
      $this->handlers[$eventKind] = $h;
   }

   /// esemény kezelése
   function handle( Event $e ) {
      if ( $h = Tools::g($this->handlers,$e->kind))
         if ( call_user_func( $h, $e )) return;
      switch ($e->kind) {
         case Event::RESIZE: return $this->resize();
      }
   }

   function coords( Point $p, $mode ) {
      switch ($mode) {
         case Coord::FROMPARENT:
            $b = $this->bounds;
            return new Point( $p->x - $b->left, $p->y - $b->top );
         case Coord::FROMWINDOW:
            $q = $this->parent()->coords( $p, Coord::FROMWINDOW );
            return $this->coords( $q, Coord::FROMPARENT );
         case Coord::TOPARENT:
            $b = $this->bounds;
            return new Point( $p->x + $b->left, $p->y + $b->top );
         case Coord::TOWINDOW: 
            $q = $this->coords( $p, Coord::TOPARENT );
            return $this->parent()->coords( $q, Coord::TOWINDOW );
         default:
            throw new EVy("Unknown mode: $mode");
      }
   }

   function viewAt( Point $at ) { return $this; }

   function invalidate( $rect = null ) {
      if ( ! $p = $this->parent ) return;
      if ( ! $rect )
         $rect = $this->bounds;
      $rect = $this->parent->border->move( $rect );
      $this->parent->invalidate( $rect );
   }

   function paint( Canvas $c ) {
      $this->paintBackground($c);
   }

   function resize() {
      if ( $h = Tools::g($this->handlers,Event::RESIZE))
         call_user_func( $h, $e );
   }

   function shown() {
      return $this->visible && $this->parent
         && $this->parent->shown();
   }

   function coord($fld) {
      switch ($fld) {
         case Coord::BOTTOM: return $this->bounds->bottom();
         case Coord::LEFT: return $this->bounds->left;
         case coord::RIGHT: return $this->bounds->right();
         case Coord::TOP: return $this->bounds->top;
         case Coord::WIDTH: return $this->bounds->width;
         case Coord::HEIGHT: return $this->bounds->height;
         default: throw new EVy("Unknown coordinate: $fld");
      }
   }

   function window() {
      if ( ! $this->parent ) return null;
      return $this->parent->window();
   }

   function __toString() {
      return "<".get_class($this).">";
   }

   protected function preferred() {
      return new Point( $this->bounds->width, $this->bounds->height );
   }

   protected function bounds() { return $this->bounds; }

   protected function setBounds( Rect $r ) {
      if ( $this->bounds->equals($r) )
         return false;
      $this->invalidate();
      $this->bounds = clone $r;
      $this->invalidate();
      $this->resize();
   }

   protected function setHovered( $x ) {
      if ( $x == $this->hovered ) return;
      $this->hovered = $x;
      $this->invalidate();
   }

   protected function paintBackground(Canvas $c) {
      $this->skin->paintBackground( $c, $this );
   }

   protected function parent() {
      if ( ! $this->parent )
         throw new EVy("Parent not set");
      return $this->parent;
   }

   protected function setParent($x) {
      if ($x && false !== $this->theme->proto)
         $this->theme->proto = $x->theme;
      if ($x && false !== $this->skin->proto)
         $this->skin->proto = $x->skin;
      $this->parent = $x;
      $this->invalidate();
   }

}
