interface vs.num.Uint64 @20240117 {

   extends BinUnit;

   check {
      next(18446744073709551615) = 0;
   }

}

