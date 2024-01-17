interface vs.num.Int64 @20240117 {

   extends BinInt;

   check {
      next(9223372036854775807) = -9223372036854775808;
   }

}

