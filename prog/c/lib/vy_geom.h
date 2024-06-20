#ifndef VY_GEOMH
#define VY_GEOMH

#include "vy.h"
#include <stdint.h>

typedef uint32_t VyColor;

void vyInitGeom( VyContext );

typedef struct Shape * Shape;

void vyShapeInit( Shape );

#endif // VY_GEOMH
