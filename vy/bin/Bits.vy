interface vs.bin.Bits @20240117 {

   extends vs.comp.Equal;
   import vs.bool.Bool;
   import vs.num.Uint;

   type D = Equal.E;

   alias U = Uint.U;

   const all:D;
   consthex;

   function {

      not(D):D;

      and(D,D):D { infix & 58; }

      or(D,D):D { infix | 56; }     

      xor(D,D):D;

      shl(D,U):D { infix << 60; }
    
      shr(D,U):D { infix >> 60; }

   }

   check {
      let a,b,c:D;
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

