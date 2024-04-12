<?php

namespace vy;

interface ExprCtx {

   const
      FUNC = "func",
      INFIX = "infix",
      INTF = "intf",
      NAME = "name";

   function checkType( $type );

   function readType( Stream $s );

   function resolve( $token, $kind );

}
