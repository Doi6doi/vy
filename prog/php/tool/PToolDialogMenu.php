<?php

namespace vy;

class PToolDialogMenu 
   extends PToolBase
   implements RunCallable
{
   
   protected $ctx;
   protected $window;
   protected $items;
   
   function __construct( $title ) {
      parent::__construct();
      $this->addFuncs( ["item"] );
      $this->funcs["exec"] = $this;
      $this->window = new Window();
      $this->window->title = $title;
   }

   function call( RunCtx $ctx, $args ) {
      $this->ctx = $ctx;
      VypGui::run();
   }

   function item( $text, $onclick=null ) {
      if ($onclick) {
         $v = new Button($text);
         $v->on( Event::CLICK, [$this,"buttonClick"] );
      } else {
         $v = new Text($text);
      }
      $this->window->add( $v );
      $this->items [] = [$v,$onclick];
      return $this;
   }
   
   function buttonClick($e) {
      foreach ($this->items as $i) {
         if ( $i[0] == $e->view ) {
            $this->window->visible = false;
            return $i[1]->call( $this->ctx, [] );
         }
      }
   }
   
}
