<?php

namespace vy;

/// hívható valami
interface RunCallable {

   function call( RunCtx $ctx, $args );

}
