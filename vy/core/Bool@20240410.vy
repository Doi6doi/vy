interface vy.core.Bool @20240410 {

   extend Equal;

   type Bool = Equal.E;

   const {
      true: Bool;
      false: Bool;
   }

   function {

      and(Bool,Bool):Bool {
         infix && 30;
      }

      or(Bool,Bool):Bool {
         infix || 20;
      }  

      not(Bool):Bool {
         prefix ! 35;
      }

      function xor(Bool,Bool):Bool;

   }

   provide {
      ! (true = false);
      given a,b:Bool;
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
