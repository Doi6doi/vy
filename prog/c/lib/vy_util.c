#include "vy_util.h"
#include "vy_random.h"
#include "vy_time.h"

#include <stdio.h>

void vyInitUtil( VyContext ctx ) {
printf("init util\n");
   vyInitRandom( ctx );
   vyInitTime( ctx );
}
