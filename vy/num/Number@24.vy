interface vy.num.Number @24 {

   extend {
      vy.core.Equal;
      vy.core.Assign;
   }

   const &dec;

   function {
      plus(:,:): { oper + }
      minus(:,:): { oper - }
      mult(:,:): { oper * }
      div(:,:): { oper / }
   }
   
   method {
      Number;
      neg: { oper - }
      pluseq(:) & { oper += }
      minuseq(:) & { oper -= }
      multeq(:) & { oper *= }
      diveq(:) & { oper /= }
   }

   provide {
      -0 = 0;
      given (a) {
         a+0 = a;
         a*0 = 0;
         a*1 = a;
         a/1 = a;
         given (b) {
            a+b = b+a;
            a-b = a + -b;
            a*b = b*a;
            given (c) {
               a*(b+c) = a*b + a*c;
               (a+b)/c = a/c + b/c;
            }
         }
      }
   }
      
}

