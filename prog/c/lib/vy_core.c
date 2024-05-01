#include "vy_core.h"
#include "vy_implem.h"
#include "vy_string.h"
#include "vy_util.h"
#include "vy_time.h"

void vyInitCore( VyContext ctx ) {
   vyAddNative( ctx, "wchar_t", sizeof( wchar_t ));
   vyAddNative( ctx, "unsigned", sizeof( unsigned ));
   vyAddNative( ctx, "size_t", sizeof( size_t ));
   vyAddNative( ctx, "bool", sizeof( bool ));
   vyAddNative( ctx, "float", sizeof(float));
   vyInitString( ctx );
   vyInitTime( ctx );
}
