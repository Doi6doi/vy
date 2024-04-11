interface vy.core.Bool @20240117 {

   extend Equal;

   type Bool = Equal.E;

   const {
      true: Bool;
      false: Bool;
   }

   function {

      and(Bool,Bool):Bool {
         infix &&;
      }

      or(Bool,Bool):Bool {
         infix ||;
      }  

      not(Bool):Bool {
         prefix !;
      }

      xor(Bool,Bool):Bool;

   }

   provide {
      ! (true = false);
      not( true ) = false;
      given ( a: Bool ) {
         and( true, a ) = a;
         and( false, a ) = false;
         or( true, a ) = true;
         or( false, a ) = a;
         not( not( a ) ) = a;
         given ( b: Bool ) {
            and( a, b ) = and( b, a );
            or( a, b ) = or( b, a );
            xor(a,b) = !(a=b);
         }
      }
   }

}
