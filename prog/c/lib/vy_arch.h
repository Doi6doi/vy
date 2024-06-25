#ifndef VYARCHH
#define VYARCHH

#include "vy.h"
#include "vy_implem.h"
#include "vy_time.h"

/// dynamic library
void * vyaLoadLibrary( VyCStr name );
void * vyaLibraryFunc( void * lib, VyCStr name );
VyCStr vyaLibraryError();

/// time
VyStamp vyaTimeStamp();
VyStamp vyaTimeAddSecond( VyStamp, float );
bool vyaTimeWaitUntil( VyStamp );



#endif // VYARCHH
