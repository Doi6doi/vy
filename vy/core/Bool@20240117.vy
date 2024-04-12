interface vy.core.Bool @20240117 {

   extend Equal;

   type Bool = Equal.Equal;

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
      true;
      ! false;
      given ( a: Bool ) {
         true && a = a;
         ! (false && a);
         true || a;
         false || a = a;
         ! ! a = a;
         given ( b: Bool ) {
            a && b = b && a;
            a || b = b || a;
            xor(a,b) = !(a=b);
         }
      }
   }

}
