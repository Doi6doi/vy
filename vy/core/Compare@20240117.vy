interface vy.core.Compare @20240117 {

   extend Equal;

   type C = Equal.E;
      
   function {

      less(C:C):B {
         infix < 50;
      }

      greater(C,C):B {
         infix > 50;
      }

      lesseq(C,C):B {
         infix <= 50;
      }

      greatereq(C,C):B {
         infix >= 50;
      }

   }

   provide {
      given a,b:C;
      a > b = b < a;
      a <= b = !(b < a);
      a >= b = !(a < b);
      xor( a=b, a<b );
   }

}

