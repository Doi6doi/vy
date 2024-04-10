<?php

/// vy szöveges fájl olvasó
class VyStream {

   const
      EOS = "eos",
      IDENT = "ident",
      NUM = "num",
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
      $this->row = 1;
      $this->col = 1;
   }

   function position() {
      return sprintf("%s (%d:%d)", $this->filename, $this->row, $this->col );
   }

   function readWS() {
      $ret = "";
      while ( self::WS == $this->nextKind() )
         $ret .= $this->read();
      return $ret;
   }

   function eos() { return $this->at >= strlen( $this->data ); }

   function nextKind() {
      if ( $this->eos() )
         return self::EOS;
      $ch = $this->nextChar(0);
      if ( $this->isWS($ch) )
         return self::WS;
      else if ( $this->isIdent($ch, true))
         return self::IDENT;
      else if ( $this->isNum($ch))
         return self::NUM;
      else if ( $this->isSymbol($ch))
         return self::SYMBOL;
      else
         throw new EVy("Unknown char: '$ch' (".ord($ch).")");
   }

   /// sor olvasása
   function readLine() {
      $ret = "";
      while ( true ) {
         $nxt = $this->nextKind();
         if ( self::EOS == $nxt )
            return $ret;
         $part = $this->read();
         $ret .= $part;
         if ( self::WS == $nxt && false !== strpos("\n", $part ))
            return $ret;
      }
   }

   function isWS( $ch ) {
      switch ($ch) {
         case " ":
         case "\t":
         case "\n":
         case "\r":
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
         case ".": case "@": case "{": case "}": case ";":
         case "=": case ",": case ":": case "(": case ")":
         case "&": case "|": case "!": case "<": case ">":
         case "[": case "]": case "+": case "-": case "*":
         case "/": case "$":
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
      $l = strlen( $this->data );
      switch ( $k = $this->nextKind() ) {
         case self::EOS: return 0;
         case self::WS: return 1;
         case self::IDENT:
            $i = 0;
            while ( $this->at+$i < $l ) {
               if ( $this->isIdent( $this->nextChar($i), 0 == $i ))
                  ++$i;
                  else return $i;
            }
            return $i;
         case self::SYMBOL: return 1;
         case self::NUM:
            $i = 0;
            while ( $this->at+$i < $l ) {
               if ( $this->isNum( $this->nextChar($i) ))
                  ++$i;
                  else return $i;
            }
         default: throw new EVy("Unknown kind: $k" );
      }
   }

   function readToken($tok) {
      if ( $tok != $this->next() )
         throw $this->notexp("'".$tok."'");
      return $this->read();
   }

   /// azonosító olvasása
   function readIdent() {
      if ( self::IDENT != $this->nextKind())
         throw $this->notexp("identifier");
      return $this->read();
   }

   /// azonosító sorozat olvasása
   function readPath( $sep="." ) {
      $ret = [ $this->readIdent() ];
      while ( $this->readIf( $sep ))
         $ret [] = $this->readIdent();
      return $ret;
   }

   function read() {
      $ret = $this->next();
      $this->at += strlen( $ret );
      $this->incPosition( $ret );
      return $ret;
   }

   function readIf( $tok ) {
      if ( $tok == $this->next() ) {
         $this->read();
         return true;
      }
      return false;
   }

   function nextChar( $i ) {
      return $this->data[ $this->at + $i ];
   }

   function readNat() {
      $ret = 0;
      while ( ! $this->eos() ) {
         $ch = $this->nextChar(0);
         if ( $this->isNum($ch)) {
            $ret = 10 * $ret + ord($ch)-ord('0');
            ++ $this->at;
         } else {
            $this->incPosition("".$ret);
            return $ret;
         }
      }
   }

   /// útvonal és feltétel olvasása
   function readPathVerCond() {
      $ret = $this->readPath();
      if ( "@" == $this->next() )
         $ret .= $this->readVerCond();
      return $ret;
   }

   /// verzió olvasása
   function readVer() {
      $this->readToken("@");
      return "@".$this->readNat();
   }

   /// verzió feltétel olvasása
   function readVerCond() {
      $ret = $this->readToken("@");
      while ( in_array( $this->next(), ["<","=",">"] ))
         $ret .= $this->read();
      $ret .= $this->readNat();
      return $ret;
   }

   function notexp( $exp ) {
      return new EVy( "$exp expected but ".$this->next()." found" );
   }

   function readAll() {
      $ret = substr( $this->data, $this->at );
      $this->at = strlen( $this->data );
      return $ret;
   }

   /// pozíció aktualizálása
   protected function incPosition( $s ) {
      $n = strlen( $s );
      for ( $i=0; $i<$n; ++$i ) {
         if ( "\n" == $s[$i] ) {
            ++ $this->row;
            $this->col = 1;
         } else {
           ++ $this->col;
         }
      }
   }



}
