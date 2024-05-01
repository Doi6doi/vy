#include <vy_implem.h>
#include "vy_random.h"

static unsigned vyRandomRandom(unsigned ) {
   vyThrow("stub RandomRandom");
}

void vyInitRandom( VyContext ctx ) {
   VYRANDOMARGS( ctx, args );
   vyArgsImpl( args, "random", vyRandomRandom );
   vyAddImplem( ctx, args );
}

