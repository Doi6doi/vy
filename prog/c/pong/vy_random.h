#ifndef VY_RANDOMH
#define VY_RANDOMH

typedef struct Random {
   float (* random)( float limit );
} Random;

VyImplemArgs randomArgs() {
   VyImplemArgs ret = vyImplemArgs( "vy.random.Random", vyVer( 20240301 ));
   vyImplemArgsType(ret,"N",vyNative("float"));
   vyImplemArgsFunc(ret,"random");
   return ret;
}

#endif // VY_RANDOMH
