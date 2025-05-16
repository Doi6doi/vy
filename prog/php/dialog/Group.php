<?php

namespace vy;

class Group
   extends View
{

   const
      BORDER = "border",
      CLIENT = "client",
      FONT = "font";

   const
      GROUP = "group",
      ITEMS = "items";

   protected $items;
   protected $font;
   protected $preferred;

   function __construct() {
      parent::__construct();
      $this->items = [];
      $this->font = Font::byOwner($this);
   }

   function __get($fld) {
      switch ($fld) {
         case self::BORDER: return $this->border();
         case self::CLIENT: return $this->client();
         case self::FONT: return $this->font;
         default: return parent::__get($fld);
      }
   }

   function __set($fld,$x) {
      switch ($fld) {
         case self::CLIENT: return $this->setClient($x);
         default: parent::__set($fld,$x);
      }
   }

   function build( $d ) {
      parent::build( $d );
      $this->buildItems( Tools::g( $d, self::ITEMS ));
   }

   function add( View $item ) {
      if ( $this == ($o = $item->parent) ) return;
      if ( $o )
         $o->remove( $item );
      $item->setParent($this);
      $this->items [] = $item;
      $this->layout(true);
      return $item;
   }

   function remove( View $item ) {
      if ( $this != $item->parent ) return;
      $i->setParent(null);
      if ( false !== $i = array_search( $item, $this->items ))
         array_splice( $this->items, $i, 1 );
      $this->layout(true);
   }

   function items() { return $this->items; }

   /// tartalom elrendezése
   function layout( $place ) {
      if ( ! $this->shown() ) 
         $place = false;
      if ( $place )
         $this->invalidate();
      if ($h = Tools::g($this->handlers, Event::LAYOUT))
         $ret = call_user_func($h,$this,$place);
         else $ret = $this->defaultLayout($place);
      return $ret;
   }

   function viewAt( Point $at ) {
      for ($i=count($this->items)-1; 0<=$i; --$i) {
         $ii = $this->items[$i];
         $ib = $ii->bounds;
         if ( $ib->contains( $at ))
            return $ii->viewAt( $ii->coords($at,Coord::FROMPARENT) );
      }
      return $this;
   }

   function coords( Point $p, $mode ) {
      switch ($mode) {
         case Coord::FROMPARENT:
            $b = $this->bounds;
            $r = $this->border();
            return new Point( $p->x - $b->left - $r->left, 
               $p->y - $b->top - $r->top );
         case Coord::TOPARENT:
            $b = $this->bounds;
            $r = $this->border();
            return new Point( $p->x + $b->left + $r->left, 
               $p->y + $b->top + $r->top );
         case Coord::TOWINDOW: 
            $q = $this->coords( $p, Coord::TOPARENT );
            return $this->parent()->coords( $q, Coord::TOWINDOW );
         default:
            return parent::coords($p,$mode);
      }
   }


   /// kirajzolás
   function paint( Canvas $c ) {
      $this->paintBackground($c);
      $this->paintItems($c);
   }

   function preferred() {
      if ( null === $this->preferred )
         $this->preferred = $this->border->increase( $this->layout(false) );
      return $this->preferred;
   }

   function resize() {
      $this->preferred = null;
      $this->layout(true);
      parent::resize();
   }

   protected function next($forw,$in) {
      if ($in && $forw && $this->items)
         return $this->items[0];
      return parent::next($forw,$in);
   }

   /// részek építése
   protected function buildItems( $d ) {
      if ( ! $d ) return;
      foreach ( $d as $di )
         $this->add( Dialog::build( $di ));
   }

   protected function border() {
      return $this->skin->border($this);
   }

   protected function client() {
      return $this->border->decrease( new Rect(0,0,
         $this->bounds->width, $this->bounds->height ));
   }

   protected function paintItems($c) {
      $cc = $c->sub( $this->client() );
      foreach ($this->items as $i) {
         if ( $i->visible ) {
            $s = $cc->sub( $i->bounds );
            $i->paint( $s );
         }
      }
   }      

   /// alap elrendezés
   protected function defaultLayout($place) {
      return Layouts::page( $this, $place );
   }      

   protected function setParent($x) {
      parent::setParent($x);
      $this->preferred = null;
      if ($x && false !== $this->font->proto)
         $this->font->proto = $x->font;
   }

}
