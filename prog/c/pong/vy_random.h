#ifndef VY_RANDOMH
#define VY_RANDOMH
#include <vy.h>

typedef struct Bool * Bool;

typedef struct RandomFun {
   float (* random)(float);
} RandomFun;

#define VYRANDOMARGS( name ) \
   VyArgs name = vyArgs( ".Random", vyVer(20240301)); \
   vyArgsType( name, "Bool", NULL ); \
   vyArgsType( name, "Number", vyNative("float") ); \
   vyArgsFunc( name, "random"); \

void vyInitRandom( VyContext );


#endif // VY_RANDOMH
