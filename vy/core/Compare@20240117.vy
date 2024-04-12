interface vy.core.Compare @20240117 {

   extend Equal;

   type C = Equal.E;
      
   function {

      less(C:C):Bool {
         infix <;
      }

      greater(C,C):Bool {
         infix >;
      }

      lesseq(C,C):Bool {
         infix <=;
      }

      greatereq(C,C):Bool {
         infix >=;
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

