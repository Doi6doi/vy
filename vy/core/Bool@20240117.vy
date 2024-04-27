interface vy.core.Bool @20240117 {

   type Bool;

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

      equal(Bool,Bool): Bool;

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
