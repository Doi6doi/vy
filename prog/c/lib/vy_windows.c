#include "vy_arch.h"
#include <windows.h>

#define DAYSEC (60.0*60*24)
#define DAYU (1000000.0*DAYSEC)
#define DAYM (1000.0*DAYSEC)
#define BUFSIZE 1024


VyStamp vyaTimeStamp() {
   return GetTickCount() / DAYU;
}

VyStamp vyaTimeAddSecond( VyStamp stamp, float delta ) {
   return stamp + delta / DAYSEC;
}

bool vyaTimeWaitUntil( VyStamp stamp ) {
   VyStamp now = vyaTimeStamp();
   if ( stamp <= now )
      return false;
   Sleep( (DWORD)(DAYM*(stamp-now)) );
   return true;
}

VyPtr vyaLoadLibrary( VyCStr name ) {
   return LoadLibraryA( name );	
}

VyPtr vyaLibraryFunc( VyPtr lib, VyCStr name ) {
   return (VyPtr)GetProcAddress( lib, name );
}

VyCStr vyaLibraryError() {
   DWORD err = GetLastError();
   static char buf[BUFSIZE];
   FormatMessageA( FORMAT_MESSAGE_FROM_SYSTEM, NULL,
      err, 0, buf, BUFSIZE, NULL );
   return buf;
}


