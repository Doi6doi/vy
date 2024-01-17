interface vs.comp.Compare @20240117 {

   extends Equal;

   type C = Equal.E;

   alias B = Bool.B;

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

   check {
      let a,b:C;
      a > b = b < a;
      a <= b = !(b < a);
      a >= b = !(a < b);
      xor( a=b, a<b );
   }






}

