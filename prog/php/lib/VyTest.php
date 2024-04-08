<?php

/// tesztelő osztály
class VyTest {

   const
      PHP = "php",
      SOURCE = "source",
      DEST = "dest";

   protected $source;
   protected $dest;

   /// egy fájl építése
   function buildFile( $dest, $source, $cmd, $args ) {
      $this->source = $source;
      $this->dest = $dest;
      $sm = $this->modified( $source );
      $dm = $this->modified( $dest );
      if ( $dm >= $sm ) return true;
      $args = preg_replace_callback( '#%([A-Za-z0-9_]+)%#',
         [$this,"buildReplace"], $args );
      return 0 == $this->exec( $cmd, $args );
   }

   /// parancssorban csere
   function buildReplace( $m ) {
      $m = $m[1];
      if ( self::SOURCE == $m )
         return $this->escape( $this->source );
      else if ( self::DEST == $m )
         return $this->escape( $this->dest );
      else
         throw new Exception("Unknown pattern: $m");
   }

   /// (max) módosítás dátuma
   function modified( $x ) {
      if ( is_array( $x ) ) {
         $ret = null;
         foreach ( $x as $i ) {
            $t = $this->modified( $i );
            if ( $ret < $t )
               $ret = $t;
         }
         return $ret;
      } else {
         if ( file_exists( $x ) )
            return filemtime( $x );
            else return null;
      }
   }

   /// fájlnev(ek) escape-elve
   function escape( $x ) {
      if ( ! $x ) {
         return "";
      } else if ( is_array( $x )) {
         $ret = "";
         foreach ( $x as $i )
            $ret .= " ".escapeshellarg( $i );
         return trim($ret);
      } else
         return escapeshellarg( $x );
   }


   /// parancs futtatása
   function exec( $cmd, $args ) {
      $this->log( "$cmd $args" );
      if ( false === passthru( "$cmd $args 2>&1", $res ))
         return false;
      return $res;
   }

   /// sor naplózása
   function log( $msg ) {
      fwrite( STDERR, "$msg\n" );
   }

   /// könyvtárváltás vagy kivétel
   function chdir( $dir ) {
      if ( ! chdir( $dir ))
         throw new Exception("Cannot change dir to ".$dir);
   }


}
