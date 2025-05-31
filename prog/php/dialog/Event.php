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
      TEXT = "text",
      RESIZE = "resize",
      SCROLL = "scroll";

   /// billentyűk
   const
      ENTER = "enter",
      ESC = "esc",
      BACKSPACE = "backspace",
      SPACE = "space",
      TAB = "tab",
      HOME = "home",
      PAGEUP = "pageup",
      DELETE = "delete",
      END = "end",
      PAGEDOWN = "pagedown",
      RIGHT = "right",
      LEFT = "left",
      DOWN = "down",
      UP = "up";

   /// módosítók
   const
      SHIFT = 0x1,
      CTRL = 0x2,
      ALT = 0x4;

   static function click( $v ) {
      return new Event( self::CLICK, $v );
   }

   public $kind;
   public $text;
   public $down;
   public $index;
   public $mod;
   public $loc;
   public $view;

   function __construct($kind=null, $view=null) {
      $this->kind = $kind;
      $this->view = $view;
   }

   function __clone() {
      $this->loc = clone $this->loc;
   }

   function __toString() {
      return sprintf("<%s %s %s>", $this->kind, $this->loc, $this->down );
   }

   function unKind() {
      return new EVy("Unknown event kind: ".$this->kind );
   }

}
