<?php

interface VyExprReader {

   function checkType( $type );

   function readType( VyStream $s );


}
