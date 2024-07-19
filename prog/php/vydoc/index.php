<?php

namespace vy;

require_once("../lib/autoload.php");

Tools::allErrors();

(new Doc())->run( $_REQUEST );
