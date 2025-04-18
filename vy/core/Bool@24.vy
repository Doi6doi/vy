interface vy.core.Bool @24 {

   const {
      true;
      false;
   }

   method {

      not : { oper ! }

      assign(:) & { oper := }

   }

   function {

      and(:,:) : { oper && }

      or(:,:) : { oper || }  

      xor(:,:) : ;

      equal(:,:) : { oper = }
   }

   provide {
      true;
      ! false;
      given (a) {
         true && a = a;
         ! (false && a);
         true || a;
         false || a = a;
         ! ! a = a;
         given (b) {
            a && b = b && a;
            a || b = b || a;
            xor(a,b) = !(a=b);
         }
      }
   }

}
