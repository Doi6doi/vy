#include "vy_geom.h"
#include "vy_color.h"
#include "vy_shape.h"
#include "vy_square.h"
#include "vy_circle.h"
#include "vy_filled.h"
#include "vy_caption.h"

void vyInitGeom( VyContext ctx ) {
   vyInitColor( ctx );
   vyInitShape( ctx );
   vyInitSquare( ctx );
   vyInitCircle( ctx );
   vyInitCaption( ctx );
   vyInitFilled( ctx );
}
