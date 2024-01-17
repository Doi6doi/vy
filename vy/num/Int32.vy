interface vs.num.Int32 @20240117 {

   extends BinInt;

   check {
      next(2147483647) = -2147483648;
   }

}

