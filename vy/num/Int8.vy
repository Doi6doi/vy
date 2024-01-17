interface vs.num.Int8 @20240117 {

   extends BinInt;

   check {
      next(127) = -128;
   }

}

