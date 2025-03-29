<?php

namespace vy;

/// eszköz alap
class PToolBase 
   extends Configable
   implements Tool
{

   protected $funcs;

   function __construct() {
      $this->funcs = [];
      $this->addFuncs( ["get","set"] );
   }

   /// üzenet kiírása
   function log( $msg ) {
      fwrite( STDERR, "$msg\n" );
   }

   /// metódushoz tartozó kiírás
   function mlog( $meth ) {
      $args = func_get_args();
      array_shift( $args );
      array_unshift( $args, $this->logFmt( $meth ) );
      $msg = call_user_func_array( "sprintf", $args );
      $this->log( $msg );
   }

   function member( $name ) {
      if ( ! $ret = Tools::g( $this->funcs, $name ))
         throw new EVy( get_class($this)." has no member $name" );
      return $ret;
   }

   function run( RunCtx $ctx ) {
      return $this;
   }

   /// függvények hozzáadása
   protected function addFuncs( array $funcs ) {
      foreach ( $funcs as $f )
         $this->funcs[$f] = new PhpFunc( [$this,$f] );
   }
   
   /// függvények törlése
   protected function delFuncs( array $funcs ) {
      foreach ( $funcs as $f ) {
         if ( Tools::g( $this->funcs, $f ))
            unset( $this->funcs[$f] ); 
      }
   }

   /// mlog formátum
   protected function logFmt( $meth ) {
      return $meth;
   }

}
