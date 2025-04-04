interface vs.bin.Bits @20240202 {

   extend vs.core.Equal;

   import vs.num.Uint;

   type {
      D = Equal.E;
      U = Uint.U;
   }

   const &hex, all:D;

   function {

      not(D):D;

      and(D,D):D { infix & 58; }

      or(D,D):D { infix | 56; }     

      xor(D,D):D;

      shl(D,U):D { infix << 60; }
    
      shr(D,U):D { infix >> 60; }

      bitLength: U;

   }

   provide {
      given a,b,c:D;
      not(not(a))=a;
      not($0)=all;
      a & $0 = $0;
      a & all = a;
      a & b = b & a;
      a & (b & c) = (a & b) & c;
      a & a  = a;
      a | $0 = a;
      a | all = a;
      a | b = b | a;
      a | (b | c) = (a | b) | c;
      a | a = a;
      xor(a,b) = ( a & not(b) ) | ( not(a) | not(b) );
   }

}

