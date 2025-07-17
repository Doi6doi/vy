<?php

namespace vy;

/// operátor (prefix, infix, postfix)
class Oper {

   const
      APP = "app",
      PRE = "pre",
      POST = "post",
      IN = "in";

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
         case "&&": return $a && $b;
         case "||": return $a || $b;
            default: throw new EVy("Cannot run operator $o");
      }
   }

   /// összeadás
   static function plus( $a, $b ) {
      if ( is_array($a) ) {
         if ( ! is_array($b) || Tools::isAssoc($b) )
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
//         case "[":
//            return "]" == $ch;
         case "?":
            return "." == $ch;
      }
      return false;
   }

   /// többjegyű operátor
   static function isOper( $token, $kind ) {
      switch ( $kind ) {
         case self::APP: return in_array( $token, ["[]"] );
         case self::PRE: return  in_array( $token, ["!","++","-","--"] );
         case self::POST: return  in_array( $token, ["++","--"] );
         case self::IN: return in_array( $token,
            [":=","=","<",">","<=",">=","!=", "+","-","*","/","||","&&","+=","-=","*=","/="] );
      }
   }

   /// értékadó operátor
   static function isAssign( $token ) {
	  return in_array( $token, [":=","+=","-=","/=","*=","++","--"] );
   }

   protected $kind;
   protected $oper;

   function kind() { return $this->kind; }

   function oper() { return $this->oper; }

   function inherit( Oper $other ) {
      $this->kind = $other->kind();
      $this->oper = $other->oper();
   }

   function read( Stream $s ) {
      $s->readWS();
      switch ( $s->next() ) {
         case self::POST: case self::PRE:
            $this->kind = $s->read();
         break;
      }
      $this->readOper( $s );
      $this->guessKind();
   }

   /// kompatibilis-e
   function checkCompatible( Oper $other ) {
      if ($this->kind != $other->kind
         || $this->oper != $other->oper)
         throw new EVy("Different operators: $this $oper");
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

   protected function guessKind() {
      $op = $this->oper;
      if ( ! $this->kind ) {
         switch ( $op ) {
            case "--": case "++":
               throw new EVy("Cannot guess oper kind: ".$op );
            case "!": $this->kind = self::PRE; break;
            case "[]": $this->kind = self::APP; break;
            default: $this->kind = self::IN;
         }
      }
      if ( ! self::isOper( $op, $this->kind ))
         throw new EVy("Invalid oper: ".$this->kind." $op");
   }

}
