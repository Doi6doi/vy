interface vy.core.Equal @20240117 {

   import Bool;

   type {
      E;
      B = Bool.B;
   }

   function {
      equal(E,E):B {
         infix =;
      }

      noteq(E,E):B {
         infix !=;
      }
   }

   provide {
      given( a,b:E ) {
         if (a=b) b=a;
         (a!=b) = !(a=b);
      }
   }
     
}
