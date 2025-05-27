<?php

namespace vy;

class Dialog {

   const
      KIND = "kind";
      
   const
      RESULT = "result";

   static function build( $params ) {
      if ( ! Tools::isAssoc($params))
         throw new EVy("Hash expected");
      $kind = Tools::g( $params, self::KIND );
      $ret = self::create( $kind );
      $ret->build( $params );
      return $ret;
   }

   static function create( $kind ) {
      switch ($kind) {
         case Button::BUTTON: return new Button();
         case Text::TEXT: return new Text();
         case Window::WINDOW: return new Window();
         default: throw new EVy("Unknown kind: $kind");
      }
   }

   protected $window;
   protected $result;

   function __construct( $params ) {
      $this->window = $w = self::build( $params );
      $this->result = [];
      if ( ! ($w instanceof Window ))
         throw new EVy("Dialog must be a window");
      $this->refine( $w );
   }

   function exec() {
      $this->result = [];
      VypGui::run();
      return array_merge( $this->window->values(), $this->result );
   }

   function buttonClick( $e ) {
      if ( $e->view && $n = $e->view->name )
         $this->result[ self::RESULT ] = $n;
      $this->window->visible = false;
   }

   protected function refine( $v ) {
      if ($v instanceof Button) {
         $v->on( Event::CLICK, [$this,"buttonClick"] );
      } else if ($v instanceof Group) {
         foreach ($v->items() as $i)
            $this->refine($i);
      }
   }
            
      


}
