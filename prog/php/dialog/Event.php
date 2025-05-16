<?php

namespace vy;

class Event {

   /// esemény fajták
   const
      BUTTON = "button",
      CLICK = "click",
      KEY = "key",
      LAYOUT = "layout",
      MOVE = "move",
      QUIT = "quit",
      TYPE = "type",
      RESIZE = "resize",
      SCROLL = "scroll";

   public $kind;
   public $down;
   public $index;
   public $loc;
   public $view;

   function __construct($kind=null) {
      $this->kind = $kind;
   }

   function __clone() {
      $this->loc = clone $this->loc;
   }

   function __toString() {
      return sprintf("<%s %s %s>", $this->kind, $this->loc, $this->down );
   }

   function unKind() {
      return new EVyg("Unknown event kind: ".$this->kind );
   }

}
