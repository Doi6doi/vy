interface vy.num.Number @24 {

   extend {
      vy.core.Equal;
      vy.core.Assign;
   }

   const &dec: Number;

   function {
      Number.neg:Number { oper - }
      plus(Number,Number):Number { oper + }
      minus(Number,Number):Number { oper - }
      mult(Number,Number):Number { oper * }
      div(Number,Number):Number { oper / }
   }
   
   method {
      Number;
      pluseq(Number) & { oper += }
      minuseq(Number) & { oper -= }
      multeq(Number) & { oper *= }
      diveq(Number) & { oper /= }
   }

   provide {
      -0 = 0;
      given ( a: Number ) {
         a+0 = a;
         a*0 = 0;
         a*1 = a;
         a/1 = a;
         given ( b: Number ) {
            a+b = b+a;
            a-b = a + -b;
            a*b = b*a;
            given( c: Number ) {
               a*(b+c) = a*b + a*c;
               (a+b)/c = a/c + b/c;
            }
         }
      }
   }
      
}

