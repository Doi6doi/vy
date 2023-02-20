<?php

/// VS írás-olvasás
class VSStream {

   const
      /// buffer olvasás
      STEP = 512;

   /// olvasási buffer
   protected $buffer;

   /// Vson-ná alakítás íráshoz
   static function toVson( $x ) {
      switch ( $t = gettype( $x ) ) {
         case "object":
            if ( $x instanceof VSCommand )
               return $x->toVson();
            else if ( $x instanceof VSHandle )
               return sprintf( "$%d:%d", $x->kind, $x->value );
            else
               throw new EVS("Unknown object: ".get_class($x));
         break;
         case "array": return self::toVsonArr( $x );
         case "NULL":
         case "string":
            return json_encode( $x );
         default:
            throw new EVS("Unknown data: $t");
      }
   }

   /// tömb vson-ként
   static function toVsonArr( array $x ) {
      $first = true;
      $isa = Tools::isAssoc( $x );
      $ret = $isa ? "{" : "[";
      foreach ( $x as $k=>$v ) {
         if ( $first )
            $first = false;
            else $ret .= ",";
         if ( $isa )
            $ret .= self::toVson($k).":";
         $ret .= self::toVson($v);
      }
      $ret .= $isa ? "}" : "]";
      return $ret;
   }

   /// elem kiírása
   function writeCommand( $x ) {
      fwrite( STDOUT, self::toVson( $x )."\n" );
   }

   /// elems olvasása
   function readCommand() {
      $v = $this->readAny();
      if ( ! $v instanceof VSCommand )
         throw new EVS("Unknown command: $v");
   }

   /// valamilyen adat olvasása
   function readAny() {
      $this->readWS();
      switch ( $c = $this->next() ) {
         default:
            throw new EVS("Unexpected char: $c");
      }
   }

   /// kitöltők olvasása
   function readWS() {
      while ( true ) {
         switch ( $c = $this->next() ) {
            case '\0': case ' ': case '\t': case '\n':
            case '\r':
               $this->chop(1);
            break;
            default: return;
         }
      }
   }

   /// következő karakterek
   function next( $n=1 ) {
      $this->grow( $n );
      return substr( $this->buffer, 0, $n );
   }

   /// buffer növelése
   protected function grow( $n = 1 ) {
      while ( strlen( $this->buffer ) < $n ) {
         $part = fread( STDIN, self::STEP );
         if ( ! $part )
            return false;
         $this->buffer .= $part;
      }
   }
}
