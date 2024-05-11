#include "vy_util.h"
#include "vy_random.h"
#include "vy_time.h"

#include <stdio.h>

void vyInitUtil( VyContext ctx ) {
   vyInitRandom( ctx );
   vyInitTime( ctx );
}
