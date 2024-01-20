<?php

/// vy szöveges fájl olvasó
class VyStream {

   const
      EOS = "eos",
      IDENT = "ident",
      SYMBOL = "symbol",
      WS = "ws";

   protected $filename;
   protected $data;
   protected $at;
   protected $row;
   protected $col;

   function __construct( $filename ) {
      $this->filename = $filename;
      $this->data = file_get_contents( $filename );
      if ( false === $this->data )
        throw new Evy("Could not load file '$filename'");
      $this->at=0;
   }

   function readWS() {
      $ret = "";
      while ( self::WS == $this->nextKind() )
         $ret .= $this->read();
      return $ret;
   }

   function nextKind() {
      if ( $this->at >= strlen( $this->data ))
         return self::EOS;
      $ch = $this->data[$this->at];
      if ( $this->isWS($ch) )
         return self::WS;
      else if ( $this->isIdent($ch, true))
         return self::IDENT;
      else if ( $this->isNum($ch))
         return self::NUM;
      else if ( $this->isSymbol($ch))
         return self::SYMBOL;
      else
         throw new EVy("Unknown char: $ch");
   }

   function isWS( $ch ) {
      switch ($ch) {
         case ' ':
         case '\t':
         case '\n':
         case '\r':
            return true;
         default:
            return false;
      }
   }

   function isNum( $ch ) {
      return '0' <= $ch && $ch <= '9';
   }

   function isSymbol( $ch ) {
      switch ($ch) {
         case ".": case "@": case "{": case "}":
            return true;
         default: return false;
      }
   }

   function isIdent( $ch, $first ) {
      if ('a' <= $ch && $ch <= 'z')
         return true;
      if ('A' <= $ch && $ch <= 'Z')
         return true;
      if ( $this->isNum( $ch ) )
         return ! $first;
      if ( '_' ==  $ch )
         return ! $first;
      return false;
   }

   function next() {
      return substr( $this->data, $this->at, $this->nextLength() );
   }

   function nextLength() {
      switch ( $k = $this->nextKind() ) {
         case self::EOS: return 0;
         case self::WS: return 1;
         case self::IDENT:
            $i = 0;
            while ( $this->at+$i < strlen( $this->data ) ) {
               if ( $this->isIdent( $this->data[$this->at+$i], 0 == $i ))
                  ++$i;
                  else return $i;
            }
            return $i;
         case self::SYMBOL: return 1;
         default: throw new EVy("Unknown kind: $k" );
      }
   }

   function readToken($tok) {
      if ( $tok != $this->next() )
         throw $this->notexp("'".$tok."'");
      return $this->read();
   }

   function readIdent() {
      if ( self::IDENT != $this->nextKind())
         throw $this->notexp("identifier");
      return $this->read();
   }

   function read() {
      $ret = $this->next();
      $this->at += strlen( $ret );
      return $ret;
   }

   function readIf( $tok ) {
      if ( $tok == $this->next() ) {
         $this->read();
         return true;
      }
      return false;
   }

   function readNat() {
      $ret = 0;
      while ( $this->at < strlen( $this->data )) {
         $ch = $this->data[ $this->at ];
         if ( $this->isNum($ch)) {
            $ret = 10 * $ret + ord($ch)-ord('0');
            ++ $this->at;
         } else
            return $ret;
      }
   }

   function readVer() {
      $this->readToken("@");
      return "@".$this->readNat();
   }

   function notexp( $exp ) {
      return new EVy( "$exp expected but ".$this->next()." found" );
   }

   function readAll() {
      $ret = substr( $this->data, $this->at );
      $this->at = strlen( $this->data );
      return $ret;
   }

}
