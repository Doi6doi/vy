interface vy.time.Time @24 {

   extend vy.core.Assign;

   import {
      vy.num.Number;
   }

   function {
      now: Time;
      waitUntil( Time );
   }

   method {
      addSecond( Number ): Time;
   }

}
