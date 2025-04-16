interface vy.core.Equal @24 {

   import Bool;

   function {
      equal(Equal,Equal):Bool { oper = }

      noteq(Equal,Equal):Bool { oper != }
   }

   provide {
      given( a,b:Equal ) {
         (a=b) = (b=a);
         (a!=b) = !(a=b);
      }
   }
     
}
