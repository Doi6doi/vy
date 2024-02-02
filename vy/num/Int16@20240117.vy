interface vy.num.Int16 @20240117 {

   extends BinInt;

   provide {
      next(32767) = -32768;
   }

}

