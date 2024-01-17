interface vy.num.Int64 @20240117 {

   extend BinInt;

   provide {
      next(9223372036854775807) = -9223372036854775808;
   }

}

