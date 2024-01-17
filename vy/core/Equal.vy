interface vy.core.Equal @20240117 {

   import Bool;

   type {
      E;
      B = Bool.B;
   }

   function {
      equal(E,E):B {
         infix = 40;
      }

      noteq(E,E):B {
         infix != 40;
      }
   }

   provide {
      given a,b:E;
      if (a=b) b=a;
      (a!=b) = !(a=b);
   }
     
}
