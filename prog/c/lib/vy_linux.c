#include "vy_arch.h"
#include <sys/time.h>
#include <stdio.h>
#include <dlfcn.h>

#define BUFSIZE 512

VyCStr NOTIME = "Cannot get time";

VyStamp vyaTimeStamp() {
   static struct timeval t;
   if ( gettimeofday( &t, NULL ) )
      vyThrow( NOTIME );
   return (1000000.0*t.tv_sec + t.tv_usec)/
      (1000000.0*60*60*24);
}

VyPtr vyaLoadLibrary( VyCStr name ) {
   static char buf[BUFSIZE];
   snprintf( buf, BUFSIZE, "lib%s.so", name );
   return dlopen( buf, RTLD_LAZY );
}

VyPtr vyaLibraryFunc( VyPtr lib, VyCStr name ) {
   return dlsym( lib, name );
}



