<?php

namespace vy;

/// operátor (prefix, infix, postfix )
class Oper {

   const
      INFIX = "infix",
      POSTFIX = "postfix",
      PREFIX = "prefix";

   /// egyezés
   static function same( Oper $a, Oper $b ) {
      if (!$a) return !$b;
      if (!$b) return false;
      return $a->kind() == $b->kind()
         && $a->oper() == $b->oper();
   }

   static function run( $o, $a, $b=null ) {
	  switch ($o) {
		 case ":=": return $b; 
		 case "+": case "+=": 
		    return self::plus( $a, $b );
		 case "++": return ++$a;
		 case "--": return --$a;
		 case "<": return $a < $b;
		 case ">": return $a > $b;
		 case "<=": return $a <= $b;
		 case ">=": return $a >= $b;
		 case "=": return $a == $b;
		 case "!=": return $a != $b;
		 case "!": return ! $a;
		 default: throw new EVy("Cannot run operator $o");
	  }
   }

   /// összeadás
   static function plus( $a, $b ) {
	  if ( is_array($a) ) {
		 if ( ! is_array( $b ))
		    $b = [$b];
		 return array_merge( $a, $b ); 
      } else if ( is_string($a)) {
         return $a . $b;
	  } else
	     return $a + $b;
   }


   /// többjegyű operátor folytatása
   static function cont( $pre, $ch ) {
	  if ( "" === $pre && 2 == strlen($ch)) {
		 $pre = $ch[0];
		 $ch = $ch[1];
	  }
      switch ($pre) {
         case "":
            switch ( $ch ) {
               case "&": case "|": case "!": case "=":
               case "<": case ">": case "+": case "-":
               case "*": case "/":
                  return true;
            }
         break;
         case "&": case "|": case ".":
            return $pre == $ch;
         case "!": case "<": case ">": case "*": case "/":
         case ":":
            return "=" == $ch;
         case "+": case "-":
            return $pre == $ch || "=" == $ch;
      }
      return false;
   }

   /// többjegyű operátor
   static function isOper( $token, $kind ) {
      if ( self::PREFIX == $kind ) {
         return in_array( $token, ["!","++","-","--"] );
      } else if ( self::INFIX == $kind ) {
         return in_array( $token, [":=","=","<",">","<=",">=","!=",
            "+","-","*","/","||","&&","+=","-=","*=","/="] );
      } else
         return false;
   }

   /// értékadó operátor
   static function isAssign( $token ) {
	  return in_array( $token, [":=","+=","-=","/=","*=","++","--"] );
   }

   protected $owner;
   protected $kind;
   protected $oper;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
   }

   function kind() { return $this->kind; }

   function oper() { return $this->oper; }

   function inherit( Oper $other ) {
      $this->kind = $other->kind();
      $this->oper = $other->oper();
   }

   function read( Stream $s ) {
      $s->readWS();
      switch ( $s->next() ) {
         case self::INFIX: case self::POSTFIX: case self::PREFIX:
            $this->kind = $s->read();
         break;
         default:
            throw $s->notexp( "operator" );
      }
      $this->readOper( $s );
// Tools::debug("READ OPER", $this->oper);
      $s->readWS();
      $s->readToken(";");
   }

   function __toString() {
      return $this->kind.$this->oper;
   }

   /// operátor olvasása
   protected function readOper( $s ) {
      $s->readWS();
      $this->oper = "";
      while ( self::cont( $this->oper, $s->next() ) )
         $this->oper .= $s->read();
      if ( ! $this->oper )
         throw $s->notexp("operator");
   }



}
