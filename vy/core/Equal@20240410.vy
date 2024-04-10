interface vy.core.Equal @20240410 {

   import Bool;

   type {
      E;
      Bool = Bool.Bool;
   }

   function {
      equal(E,E):Bool {
         infix = 40;
      }

      noteq(E,E):Bool {
         infix != 40;
      }
   }

   provide {
      given a,b:E;
      if (a=b) b=a;
      (a!=b) = !(a=b);
   }
     
}
