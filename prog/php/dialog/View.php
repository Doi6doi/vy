<?php

namespace vy;

class View {

   const
       BOUNDS = "bounds",
       FOCUSED = "focused",
       HOVERED = "hovered",
       IMPL = "impl",
       NAME = "name",
       PARENT = "parent",
       PREFERRED = "preferred",
       THEME = "theme",
       SKIN = "skin",
       VALUE = "value",
       VISIBLE = "visible";

   protected $parent;
   protected $name;
   protected $visible;
   protected $impl;
   protected $bounds;
   protected $theme;
   protected $skin;
   protected $handlers;
   protected $hovered;
   protected $focused;

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
         case self::FOCUSED: return $this->focused;
         case self::HOVERED: return $this->hovered;
         case self::IMPL: return $this->impl;
         case self::NAME: return $this->name;
         case self::PARENT: return $this->parent;
         case self::PREFERRED: return $this->preferred();
         case self::THEME: return $this->theme;
         case self::SKIN: return $this->skin;
         case self::VALUE: return null;
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
         case self::NAME: return $this->name = $x;
         case self::VISIBLE: return $this->setVisible($x);
         default: throw Tools::unFld($fld);
      }
   }

   function build( $d ) {
      $this->theme->build( Tools::g( $d, self::THEME ));
      $this->skin->build( Tools::g( $d, self::SKIN ));
      $this->bounds->build( Tools::g( $d, self::BOUNDS ));
      Tools::buildProps($this, $d, [self::NAME, self::VISIBLE] );
      $this->buildHandlers( $d );
   }

   /// eseménykezelő beállítása
   function on(string $eventKind, $h) {
      $this->handlers[$eventKind] = $h;
   }

   /// esemény kezelése
   function handle( Event $e ) {
      if ( $this->callHandler($e->kind, [$e] ))
         return;
      switch ($e->kind) {
         case Event::RESIZE: 
            return $this->resize();
         case Event::KEY:
            return $this->key( $e );
         case Event::TEXT:
            return $this->type( $e );
         case Event::MOVE:
         case Event::BUTTON:
            return $this->pointer( $e );
         case Event::CLICK:
            return $this->click( $e );
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

   function focusable() { return false; }

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

   /// eseménykezelő hívása
   protected function callHandler( $kind, array $args = [] ) {
      if ( ! $h = Tools::g( $this->handlers, $kind ))
         return false;
      if ( is_callable( $h ))
         return call_user_func_array( $h, $args );
      throw new EVy("Unknown handler: ".Tools::flatten($h));
   }         

   protected function resize() {
   }

   protected function pointer( $e ) {
      $this->window()->setHovered( $this );
   }

   protected function type( Event $e ) {
      if ($this->parent)
         return $this->parent->type( $e );
   }      

   protected function key( Event $e ) {
      if ( $e->down ) {
         switch ( $e->text ) {
            case Event::TAB:
               switch ($e->mod) {
                  case Event::SHIFT:
                  case 0:
                     if ( $this->tab( 0 == $e->mod )) return true;
                  break;
               }
            break;
         }
      }
      if ($this->parent)
         return $this->parent->key( $e );
   }

   protected function click( $e ) {
      if ( ! $this->focusable() ) return;
      $this->window()->focused = $this;
   }

   /// elrejtés, vagy felfedés
   protected function setVisible($x) {
      if ( $this->visible == $x ) return;
      $this->visible = !! $x;
      $this->invalidate();
   }

   /// tab billentyű
   protected function tab( $forw ) {
      $this->window()->focusNext($forw);
      return true;
   }

   /// soron következő elem
   protected function next($forw,$in) {
      if ( ! $p = $this->parent ) return null;
      $pis = $p->items();
      $i = array_search( $this, $pis );
      if ( $forw && $i+1 < count($pis))
         return $pis[$i+1];
      else if ( ! $forw && 0 < $i )
         return $pis[$i-1];
      else
         return $p->next($forw,false);
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

   protected function setFocused( $x ) {
      if ( $x == $this->focused ) return;
      $this->focused = $x;
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
      if ( $this->parent == $x ) return;
      if ( $this->focused )
         $this->windowFocus(false);
      if ($x && false !== $this->theme->proto)
         $this->theme->proto = $x->theme;
      if ($x && false !== $this->skin->proto)
         $this->skin->proto = $x->skin;
      $this->parent = $x;
      $this->windowFocus(true);
      $this->invalidate();
   }

   /// ablak fókusz módosítása, ha érintett
   protected function windowFocus($on) {
      if ( ! $w = $this->window() ) return;
      if ( $on && ! $w->focused )
         $w->focusNext(true);
      else if ( ! $on && $this->focused )
         $w->focused = null;
   }

   /// kezelők építése
   protected function buildHandlers( $d ) {
      foreach ( $d as $k=>$v ) {
         if ( "on" == substr( $k,0,2 ))
            $this->on( substr($k,2), $v );
      }
   }

}
