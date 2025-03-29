<?php

namespace vy;

/// más eszköz(öke)t használó eszköz
class PToolChoice extends PToolBase {

   const
      CHOICE = "choice";

   protected $choice;
   
   function get( $fld ) {
      return $this->choice->get( $fld );
   }
   
   function set( $fld, $val = true ) {
      if ( self::CHOICE == $fld )
         $this->setChoice( $val );
      else if ( Tools::isAssoc( $fld ))
         parent::set( $fld, $val );
      else
         $this->choice->set( $fld, $val );
   }
   
   /// aleszköz kiválasztása
   protected function setChoice( $val ) {
      $this->choice = $val;
      $this->updateFuncs();
   }

   protected function addFuncs( array $funcs ) {
      parent::addFuncs( $funcs );
      $this->updateFuncs();
   }

   /// a függvények átállítása a kiválasztottéra
   protected function updateFuncs() {
      foreach ( $this->funcs as $k => $v ) {
         $clb = [$this->choice,$k];
         if ( ! is_callable( $clb ))
            $clb = [$this,$k];
         $this->funcs[ $k ] = new PhpFunc( $clb );
      }
   }

}
