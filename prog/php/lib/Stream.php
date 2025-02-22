<?php

namespace vy;

/// vy szöveges fájl olvasó
class Stream {

   const
      EOS = "eos",
      IDENT = "ident",
      NUM = "num",
      SYMBOL = "symbol",
      STR = "str",
      WS = "ws";

   /// string escape
   static function escape( string $s ) {
	  $ret = "";
	  $n = strlen($s);
	  for ($i=0; $i<$n; ++$i) {
		 switch ( $ch = $s[$i] ) {
		    case "\r": $ch = "\\r"; break;
		    case "\n": $ch = "\\n"; break;
		    case "\b": $ch = "\\b"; break;
		    case "\t": $ch = "\\t"; break;
		    case "\"": $ch = "\\\""; break;
		    case "\\": $ch = "\\\\"; break;
		 }
		 $ret .= $ch;
      }
      return "\"$ret\"";
   }

   /// string unescape
   static function unescape( string $s ) {
	  $n = strlen( $s );
	  if ( 2 > $n || '"' != $s[0] || '"' != $s[$n-1] ) 
	     throw new EVy("Cannot unescape $s");
	  $ret = "";
	  for ($i=1; $i<$n-1; ++$i) {
		 $ch = $s[$i];
		 if ( "\\" == $ch ) {
		    $ch = $s[++$i];
		    switch ( $ch ) {
		       case "r": $ch = "\r"; break;
		       case "n": $ch = "\n"; break;
		       case "b": $ch = "\b"; break;
		       case "t": $ch = "\t"; break;
		    }
		 }
		 $ret .= $ch;
      }
      return $ret;
   }

   protected $filename;
   protected $data;
   protected $at;
   protected $row;
   protected $col;

   function __construct( $filename ) {
      $this->filename = $filename;
      $this->data = file_get_contents( $filename );
      if ( false === $this->data )
        throw new EVy("Could not load file '$filename'");
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

   function close() { 
	  $this->at = 0; 
	  $this->data = "";
   }

   function nextKind() {
      if ( $this->eos() )
         return self::EOS;
      $ch = $this->nextChar(0);
      if ( $this->isWS() )
         return self::WS;
      else if ( $this->isIdent($ch, true))
         return self::IDENT;
      else if ( $this->isNum($ch))
         return self::NUM;
      else if ( $this->isSymbol($ch))
         return self::SYMBOL;
      else if ( $this->isString($ch))
         return self::STR;
      else
         throw new EVy("Unknown char: '$ch' (".ord($ch).")");
   }

   function isWS() {
	  $ch = $this->nextChar(0);
      switch ($ch) {
         case " ":
         case "\t":
         case "\n":
         case "\r":
            return true;
         case "/":
            $ch2 = $this->nextChar(1);
            return "/" == $ch2 || "*" == $ch2;
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
   
   function isString( $ch ) {
	  return "\"" == $ch;
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
         case self::WS: 
            if ( "/" != $this->nextChar(0) )
               return 1;
            $e = ( "/" == $this->nextChar(1) ? "\n" : "*/" );
			if ( $i = strpos( $this->data, $e, $this->at ))
			   return $i - $this->at + strlen($e);
			return $l - $this->at;
         return 1;
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
            return $i;
         case self::STR:
            for ( $i = 1; $this->at+$i < $l; ++$i ) {
			   if ( "\"" == $this->nextChar($i)
			      && "\\" != $this->nextChar($i-1)
			   )
			      return $i+1;
			}
			return $i;
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
   function readIdents( $sep ) {
      $ret = [ $this->readIdent() ];
      $this->readWS();
      while ( $this->readIf( $sep ) ) {
         $ret [] = $this->readIdent();
         $this->readWS();
      }
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
      $ret = $this->readIdents(".");
      if ( "@" == $this->next() )
         $ret .= Version::read( $this, true );
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
