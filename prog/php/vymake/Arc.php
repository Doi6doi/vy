<?php

namespace vy;

/// arc csomagoló
class Arc
   extends Configable
{
	
   const
      PROGRESS = "progress",
      SAME = "same";

   function extract( $src, $dst ) {
	  $z = new \ZipArchive();
	  if ( true !== $z->open( $src ))
	     throw new EVy("Cannot open archive: $src");
	  $same = $this->get( self::SAME );
	  $n = $z->numFiles;
	  $sum = $this->sumSizes($z);
	  $at = 0;
	  for ($i=0; $i<$n; ++$i) {
		 if ( $same || ! $this->isSame( $z, $i, $dst )) {
            $name = $z->getNameIndex($i);
            $this->prgr( Tools::percent( $at, $sum ).": $name ", false);
		    if ( ! $z->extractTo( $dst, $name ))
		       throw new EVy("Could not extract: $name");
		 }
		 $at += $this->size($z,$i);
      }
      $z->close();
   }

   protected function confKind( $fld ) {
	  switch ( $fld ) {
		 case self::SAME:
		    return Configable::BOOL;
		 case self::PROGRESS: 
		    return Configable::ANY;
      }
   }

   /// ugyanaz-e a csomagolt áfjl, mint a kinti
   protected function isSame( $z, $i, $dst ) {
      $name = $z->getNameIndex($i);
      $sz = $this->size( $z, $i );
	  $df = Tools::path( $dst, $name );
	  if ( file_exists( $df ) && filesize( $df ) == $sz )
	     return true;
	  return false;
   }

   /// kicsomagolt méretek összege
   protected function sumSizes( $z ) {
	  $ret = 0;
	  $n = $z->numFiles;
	  for ($i=0; $i<$n; ++$i) {
		 $name = $z->getNameIndex($i);
		 $ret += $this->size($z,$i);
	  }
	  return $ret;
   }
	  
   /// egy fájl kicsomagolt mérete
   protected function size( $z, $i ) {
      $s = $z->statIndex($i);
	  return $s["size"];
   }

   /// progress function
   protected function prgr( $msg ) {
	  Tools::debug( $msg );
   }

}
