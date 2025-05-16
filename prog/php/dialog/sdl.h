
#include <stdint.h>

typedef uint8_t Uint8;
typedef uint32_t Uint32;
typedef int32_t Sint32;
typedef int64_t Sint64;

typedef Uint32 SDL_WindowID;

typedef struct SDL_PixelFormat SDL_PixelFormat;

typedef struct SDL_Renderer SDL_Renderer;

typedef struct SDL_RWops SDL_RWops;

typedef struct SDL_Surface SDL_Surface;

typedef struct SDL_Texture SDL_Texture;

typedef struct SDL_Window SDL_Window;

typedef struct SDL_Rect {
    int x, y;
    int w, h;
} SDL_Rect;

typedef struct SDL_Color {
    Uint8 r;
    Uint8 g;
    Uint8 b;
    Uint8 a;
} SDL_Color;

typedef Sint64 SDL_TouchID;

typedef Sint64 SDL_FingerID;

typedef struct SDL_MouseMotionEvent {
    Uint32 type;
    Uint32 timestamp;
    Uint32 windowID;
    Uint32 which;
    Uint32 state;
    Sint32 x;
    Sint32 y;
    Sint32 xrel;
    Sint32 yrel;
} SDL_MouseMotionEvent;

typedef struct SDL_MouseButtonEvent {
    Uint32 type;
    Uint32 timestamp;
    Uint32 windowID;
    Uint32 which;
    Uint8 button;
    Uint8 state;
    Uint8 clicks;
    Uint8 padding1;
    Sint32 x;
    Sint32 y;
} SDL_MouseButtonEvent;

typedef struct SDL_TouchFingerEvent {
    Uint32 type;
    Uint32 timestamp;
    SDL_TouchID touchId;
    SDL_FingerID fingerId;
    float x;
    float y;
    float dx;
    float dy;
    float pressure;
    Uint32 windowID;
} SDL_TouchFingerEvent;

typedef struct SDL_WindowEvent {
    Uint32 type;
    Uint32 timestamp;
    Uint32 windowID;
    Uint8 event;
    Uint8 padding1;
    Uint8 padding2;
    Uint8 padding3;
    Sint32 data1;
    Sint32 data2;
} SDL_WindowEvent;

typedef union SDL_Event {
    Uint32 type;
    SDL_MouseButtonEvent button;
    SDL_MouseMotionEvent motion;
    SDL_TouchFingerEvent tfinger;
    SDL_WindowEvent window;
} SDL_Event;



SDL_Renderer * SDL_CreateRenderer(SDL_Window * window,
   int index, Uint32 flags);
SDL_Texture * SDL_CreateTextureFromSurface(SDL_Renderer * renderer,
   SDL_Surface * surface);
SDL_Window * SDL_CreateWindow(const char *title, int x, int y, int w,
   int h, Uint32 flags);
void SDL_DestroyRenderer(SDL_Renderer * renderer);
void SDL_DestroyTexture(SDL_Texture * texture);
void SDL_FreeSurface(SDL_Surface * surface);
int SDL_GetDisplayBounds(int displayIndex, SDL_Rect * rect);
const char* SDL_GetError(void);
int SDL_GetWindowBordersSize(SDL_Window * window,
   int *top, int *left, int *bottom, int *right);
SDL_WindowID SDL_GetWindowID(SDL_Window *window);
void SDL_GetWindowPosition(SDL_Window * window, int *x, int *y);
void SDL_GetWindowSize(SDL_Window * window, int *w, int *h);
void SDL_HideWindow(SDL_Window * window);
Uint32 SDL_MapRGB(const SDL_PixelFormat * format, Uint8 r, Uint8 g, Uint8 b);
int SDL_Init(Uint32 flags);
int SDL_PollEvent(SDL_Event * event);
int SDL_QueryTexture(SDL_Texture * texture,
 Uint32 * format, int *access, int *w, int *h);
SDL_RWops* SDL_RWFromFile(const char *file,  const char *mode);
int SDL_RenderCopy(SDL_Renderer * renderer, SDL_Texture * texture,
   const SDL_Rect * srcrect, const SDL_Rect * dstrect);
int SDL_RenderDrawRect(SDL_Renderer * renderer, const SDL_Rect * rect);
int SDL_RenderFillRect(SDL_Renderer * renderer, const SDL_Rect * rect);
void SDL_RenderPresent(SDL_Renderer * renderer);
int SDL_RenderSetClipRect(SDL_Renderer * renderer, const SDL_Rect * rect);
int SDL_SaveBMP_RW(SDL_Surface * surface, SDL_RWops * dst, int freedst);
int SDL_SetRenderDrawColor(SDL_Renderer * renderer,
   Uint8 r, Uint8 g, Uint8 b, Uint8 a);
void SDL_SetWindowTitle(SDL_Window * window, const char *title);
void SDL_ShowWindow(SDL_Window * window);
void SDL_Quit();
