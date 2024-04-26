#ifndef VY_FONTH
#define VY_FONTH
#include <vy.h>

typedef struct FontFun {
} * FontFun;

#define VYFONTARGS( name ) \
   VyImplemArgs name = vyImplemArgs( ".Font", vyVer(20240301)); \


#endif // VY_FONTH
