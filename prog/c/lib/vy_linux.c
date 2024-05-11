#include "vy_arch.h"
#include <sys/time.h>
#include <stdio.h>
#include <dlfcn.h>
#include <unistd.h>

#define BUFSIZE 512

VyCStr NOTIME = "Cannot get time";

#define DAYSEC (60.0*60*24)
#define DAYU (1000000.0*DAYSEC)

VyStamp vyaTimeStamp() {
   static struct timeval t;
   if ( gettimeofday( &t, NULL ) )
      vyThrow( NOTIME );
   return (1000000.0*t.tv_sec + t.tv_usec) / DAYU;
}

VyStamp vyaTimeAddSecond( VyStamp stamp, float delta ) {
   return stamp + delta * DAYSEC;
}

bool vyaTimeWaitUntil( VyStamp stamp ) {
   VyStamp now = vyaTimeStamp();
   if ( stamp <= now )
      return false;
   usleep( (stamp-now)*DAYU );
   return true;
}




VyPtr vyaLoadLibrary( VyCStr name ) {
   static char buf[BUFSIZE];
   snprintf( buf, BUFSIZE, "lib%s.so", name );
   return dlopen( buf, RTLD_LAZY );
}

VyPtr vyaLibraryFunc( VyPtr lib, VyCStr name ) {
   return dlsym( lib, name );
}

VyCStr vyaLibraryError() {
   return dlerror();
}



