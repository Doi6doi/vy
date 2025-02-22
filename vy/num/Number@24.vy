interface vy.num.Number @24 {

   extend vy.core.Equal;

   type Number = Equal.Equal;

   const &dec: Number;

   function {
      neg(Number):Number {
         prefix -;
      }
      plus(Number,Number):Number {
         infix +;
      }
      minus(Number,Number):Number {
         infix -;
      }
      mult(Number,Number):Number {
         infix *;
      }
      div(Number,Number):Number {
         infix /;
      }
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

