<?php

require_once("../lib/autoload.php");

Tools::allErrors();

(new VyDoc())->run( $_REQUEST );
