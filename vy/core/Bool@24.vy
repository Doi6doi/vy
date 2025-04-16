interface vy.core.Bool @24 {

   const {
      true: Bool;
      false: Bool;
   }

   method {

      not:Bool { prefix ! }

      assign(Bool) & { oper := }

   }

   function {

      and(Bool,Bool):Bool { oper && }

      or(Bool,Bool):Bool { oper || }  


      xor(Bool,Bool):Bool;

      equal(Bool,Bool): Bool { oper = }
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
