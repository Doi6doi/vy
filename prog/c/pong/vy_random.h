#ifndef VY_RANDOMH
#define VY_RANDOMH
#include <vy.h>

typedef struct Bool * Bool;

typedef struct RandomFun {
   float (* random)(float   );
} RandomFun;

#define VYRANDOMARGS( name ) \
   VyImplemArgs name = vyImplemArgs( ".Random", vyVer(20240301)); \
   vyImplemArgsType( name, "Bool", NULL ); \
   vyImplemArgsType( name, "Number", vyNative("float") ); \
   vyImplemArgsFunc( name, "random"); \


#endif // VY_RANDOMH
