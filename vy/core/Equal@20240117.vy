interface vy.core.Equal @20240117 {

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
