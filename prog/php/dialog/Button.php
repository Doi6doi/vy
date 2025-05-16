<?php

namespace vy;

/// nyomógomb
class Button extends Group {

   const
      BUTTON = "button",
      FONT = "font",
      TEXT = "text";

   protected $text;
   protected $bitmap;

   function __construct($text=null) {
      parent::__construct();
      if (null !== $text)
         $this->text()->text = $text;
   }

   function __get($fld) {
      switch ($fld) {
         case self::TEXT: return $this->text()->text;
         default: return parent::__get($fld);
      }
   }

   function __set($fld,$x) {
      switch ($fld) {
         case self::TEXT: return $this->text()->text = $x;
         default: return parent::__set($fld,$x);
      }
   }

   function build( $d ) {
      parent::build($d);
      Tools::buildProps($this,$d,[self::TEXT]);
   }

   function paint(Canvas $c) {
      $this->paintBackground($c);
      $this->skin->paintButton( $c, $this );
      $this->paintItems($c);
   }

   function key( Event $e ) {
      if ( $e->down && 0 == $e->mod) {
         switch ( $e->text ) {
            case Event::ENTER:
            case Event::SPACE:
               $this->click();
            return true;
         }
      }
      return parent::key($e);
   }

   function viewAt( Point $at ) { return $this; }

   function coords( Point $p, $mode ) { return View::coords($p,$mode); }

   function focusable() { return true; }

   /// a szöveges rész
   protected function text() {
      if ( ! $this->text )
         $this->text = $this->add( new Text() );
      return $this->text;
   }

}
