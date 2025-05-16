
#include <stdint.h>

typedef uint8_t Uint8;

typedef struct SDL_Color {
    Uint8 r;
    Uint8 g;
    Uint8 b;
    Uint8 a;
} SDL_Color;

typedef struct SDL_Surface SDL_Surface;

typedef struct TTF_Font TTF_Font;

void TTF_CloseFont(TTF_Font *font);
int TTF_FontAscent(const TTF_Font *font);
int TTF_FontDescent(const TTF_Font *font);
int TTF_Init();
TTF_Font * TTF_OpenFont(const char *file, int ptsize);
SDL_Surface *TTF_RenderUTF8_Blended(TTF_Font *font, const char *text, SDL_Color fg);
SDL_Surface *TTF_RenderUTF8_Solid(TTF_Font *font, const char *text, SDL_Color fg);
int TTF_SetFontSize(TTF_Font *font, int ptsize);
int TTF_SizeUTF8(TTF_Font *font, const char *text, int *w, int *h);
void TTF_Quit();
