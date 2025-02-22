interface vy.core.Equal @24 {

   type Equal;

   import Bool;

   function {
      equal(Equal,Equal):Bool {
         infix =;
      }

      noteq(Equal,Equal):Bool {
         infix !=;
      }
   }

   provide {
      given( a,b:Equal ) {
         (a=b) = (b=a);
         (a!=b) = !(a=b);
      }
   }
     
}
