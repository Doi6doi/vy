interface vy.core.Compare @20240117 {

   extend Equal;

   function {

      less(Compare,Compare):Bool { oper < }

      greater(Compare,Compare):Bool { oper > }

      lesseq(Compare,Compare):Bool { oper <= }

      greatereq(Compare,Compare):Bool { oper >= }

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

