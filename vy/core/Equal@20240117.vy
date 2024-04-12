interface vy.core.Equal @20240117 {

   import Bool;

   type {
      E;
      Bool = Bool.Bool;
   }

   function {
      equal(E,E):Bool {
         infix =;
      }

      noteq(E,E):Bool {
         infix !=;
      }
   }

   provide {
      given( a,b:E ) {
         (a=b) = (b=a);
         (a!=b) = !(a=b);
      }
   }
     
}
