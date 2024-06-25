#include "vy_transform.h"
#include "vy_transformed.h"
#include "vy_square.h"
#include "vy_circle.h"

void vyInitGeom2( VyContext ctx ) {
   vyInitTransform( ctx );
   vyInitTransformed( ctx );
   vyInitSquare( ctx );
   vyInitCircle( ctx );
}
