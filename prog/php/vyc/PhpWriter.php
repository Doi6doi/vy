<?php

namespace vy;

class PhpWriter 
   extends CompWriter
{
	
   protected function writeItem() {
      $c = $this->item;
      $this->writePhp();
      $this->writePkg( $c->pkg() );
      $this->writel();
      $this->writeCmt( $c->ver() );
      $this->writeCmt( $c->comment() );
      $this->write( $this->itemKind()." ".$this->repr( $c->name() ) );
      $this->writeInherit();
      $this->writeBlock(true);
      $this->writeMembers();
      $this->writeProvides();
      $this->writeBlock(false);
   }

   protected function writePhp() {
      $this->writel("<?php");
   }
   
   protected function writePkg( $pkg ) {
      if ( ! $pkg ) return;
      $this->writel();
      $this->writel("namespace %s;", str_replace(".","\\",$pkg));
   }
   
   /// kiírandó elem fajtája
   protected function itemKind() {
      switch ($c = get_class($this->item)) {
         case Cls::class: return "class";
         case Interf::class: return "interface";
         default: throw new EVy("Unknown item: $c");
      }
   }

   /// tagok kiírása
   protected function writeMembers() {
      $last = null;
      foreach ( $this->item->funcs() as $f )
         $this->writeMember( $f, $last );
   }

   /// provide rész kiírása
   protected function writeProvides() {
   }

   protected function writeInherit() {
      $es = [];
      $is = [];
      if ( ! $x = $this->item->xtends() ) {
         if ( $x instanceof Interf )
            $is [] = $x->name();
            else $es [] = $x->name();
      }
      if ( ! $es && ! $is ) return;
      $this->writel();
      $this->stream->indent(true);
      if ( $es )
         $this->writel( "extends %s", implode(",",$es));
      if ( $is )
         $this->writel( "implements %s", implode(",",$is));
      $this->stream->indent(false);
   }
   
   /// konstansok kiírása
   protected function writeConsts() {
      if ( ! $cs = $this->item->consts() ) return;
      $this->writel("const");
      for ($i=0; $i<count($cs); ++$i) {
         $this->writeCons($c);
         if ( $i+1 == count($cs))
            this->write(";\n");
            else $this->write(",\n");
      }
   }
/*   
   /// itt definiált típus
   function own( $name ) {
      if ( $t = Tools::g( $this->map, $name ))
         return "*" == substr( $t, 0, 1 );
      return false;
   }
   
   /// egy típus reprezentációja
   function repr( $name ) {
      $t = "".Tools::g( $this->map, $name );
      if ( "*" == substr( $t, 0, 1 ))
         $t = substr( $t, 1 );
      if ( ! $t )
         $t = $name;   
      return $this->getRepr( $t );
   }

   /// egy reprezentáció
   protected function getRepr( $name ) {
      $rs = $this->reprs;
      for ($i = count($rs)-1; 0 <= $i; --$i) {
         if ( $ret = $rs[$i]->get($name))
            return $ret;
      }
      return null;
   }

   /// a modul neve
   protected function module() {
      return pathinfo( $this->filename(), PATHINFO_FILENAME );
   }
*/
      
		 
}
