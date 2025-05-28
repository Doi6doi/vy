
typedef struct WINDOW WINDOW;
typedef unsigned int wint_t;

int cbreak();
int clear();
int endwin();
int getmaxx( WINDOW * );
int getmaxy( WINDOW * );
int get_wch( wint_t * wch );
WINDOW * initscr();
int keypad(WINDOW *win, bool bf);
int noecho();
int refresh();

