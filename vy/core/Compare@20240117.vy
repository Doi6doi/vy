interface vy.core.Compare @20240117 {

   extend Equal;

   function {

      less(Compare,Compare):Bool { infix < }

      greater(Compare,Compare):Bool { infix > }

      lesseq(Compare,Compare):Bool { infix <= }

      greatereq(Compare,Compare):Bool { infix >= }

   }

   provide {
      given( a,b:Compare ) {
         (a > b) = (b < a);
         (a <= b) = !(b < a);
         (a >= b) = !(a < b);
         if ( a < b ) a != b;
      }
   }

}

