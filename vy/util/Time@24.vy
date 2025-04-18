interface vy.time.Time @24 {

   extend vy.core.Assign;

   import {
      vy.num.Number;
   }

   function {
      now: ;
      waitUntil( : );
   }

   method {
      addSecond( Number ): ;
   }

}
