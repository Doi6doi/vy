#include <vy_implem.h>
#include "vy_random.h"

static unsigned vyRandomRandom( unsigned i ) {
   vyThrow("stub vyRandomRandom");
   return i;
}

void vyInitRandom( VyContext ctx ) {
   VYRANDOMARGS( ctx, args );
   vyArgsImpl( args, "random", vyRandomRandom );
   vyAddImplem( ctx, args );
}

