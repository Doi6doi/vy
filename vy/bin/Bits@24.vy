interface vs.bin.Bits @24 {

   extend vs.core.Equal;

   import vs.num.Uint;

   const {
      &hex: Bits;
      all: Bits;
   }

   function {

      Bits.not: Bits;

      and(Bits,Bits):Bits { oper & }

      or(Bits,Bits):Bits { oper | }     

      xor(Bits,Bits):Bits;

      shl(Bits,Uint):Bits { oper << }
    
      shr(Bits,Uint):Bits { oper >> }

      bitLength: Uint;

   }

   provide {
      given a: Bits {
         a.not.not = a;
         $0.not = all;
         a & $0 = $0;
         a & all = a;
         a & a  = a;
         a | $0 = a;
         a | a = a;
         a | all = a;
         given b: Bits {
            a & b = b & a;
            a | b = b | a;
            xor(a,b) = ( a & b.not ) | ( a.not & b );
            given c: Bits {
               a & (b & c) = (a & b) & c;
               a | (b | c) = (a | b) | c;
            }
         }
      }
   }

}

