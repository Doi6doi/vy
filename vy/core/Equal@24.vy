interface vy.core.Equal @24 {

   import Bool;

   function {
      equal(:,:):Bool { oper = }

      noteq(:,:):Bool { oper != }
   }

   provide {
      given (a,b) {
         (a=b) = (b=a);
         (a!=b) = !(a=b);
      }
   }
     
}
