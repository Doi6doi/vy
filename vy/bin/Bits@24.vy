interface vs.bin.Bits @24 {

   extend vs.core.Equal;

   import vs.num.Uint;

   const {
      &hex;
      none;
      all;
   }

   method {

      not : ;

      shl(Uint): { oper << };

      shr(Uint): { oper >> };

      bitLength: Uint;

   }


   function {

      and(:,:): { oper & }

      or(:,:): { oper | }     

      xor(:,:): ;

   }

   provide {
      none.not = all;
      given (a) {
         a.not.not = a;
         a & none = none;
         a & all = a;
         a & a  = a;
         a | none = a;
         a | a = a;
         a | all = a;
         given (b) {
            a & b = b & a;
            a | b = b | a;
            xor(a,b) = ( a & b.not ) | ( a.not & b );
            given (c) {
               a & (b & c) = (a & b) & c;
               a | (b | c) = (a | b) | c;
            }
         }
      }
   }

}

