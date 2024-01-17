interface vy.core.Bool @20240117 {

   extend Equal;

   type B = Equal.E;

   const true, false: B;

   function {

      and(B,B):B {
         infix && 30;
      }

      or(B,B):B {
         infix || 20;
      }  

      not(B):B {
         prefix ! 35;
      }

      function xor(B,B):B;

   }

   provide {
      ! (true = false);
      given a,b:B;
      and( true, a ) = a;
      and( false, a ) = false;
      and( a, b ) = and( b, a );
      or( true, a ) = true;
      or( false, a ) = a;
      or( a, b ) = or( b, a );
      not( true ) = false;
      not( not( a ) ) = a;
      xor(a,b) = !(a=b);
   }

}
