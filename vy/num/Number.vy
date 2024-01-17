interface vs.num.Number @20240117 {

   extends Equals;

   type N = Equals.E;

   const 0,1:N;

   function {
      function neg(N):N {
         prefix - 80;
      }
      function plus(N,N):N {
         infix + 80;
      }
      function minus(N,N):N {
         infix - 80;
      }
      function mult(N,N):N {
         infix * 90;
      }
      function div(N,N):N {
         infix / 90;
      }
   }

   check {
      let a,b,c:N;
      -0 = 0;
      a+b = b+a;
      a+0 = a;
      a-b = a + -b;
      a*0 = 0;
      a*1 = a;
      a*b = b*a;
      a*(b+c) = a*b + a*c;
      a / 1 = a;
      (a+b)/c = a/c + b/c;
   }
      
}

