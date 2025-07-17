informal vyb.Language @25 {

Vyb binary language
===================

The binary language describes a binary object structure. Those objects
are byte-based, can be stored in memory, on disk or transferred through
the internet.

Single byte
-----------

A byte constant is written as a dollar sign an two hexademical digits:

`$61` means byte 97, the letter "a" in ASCII

A dollar sign without hex digits stands for "any byte"

`$` means any byte

Block
-----

A block is a name and a type in parentheses. The type can be explained
earlier or later in the document

`(size:int)` means a block representing an integer called `size`

The name can be omitted if it clear what the block refers to

A block can also be built of parts, contcatenating them

`(pair:(first:int)$2c(second:int))` means a pair of two ints separated by a byte 44 (a comma)

Repetition
----------

A part (constant, block, etc...) followed by a number or a name in braces
means it is repeated

`$00{7}˙ means 7 bytes of zero

`(int){size}` means size number of ints. size can be defined earlier or later
in the document

Star
----

The star symbol can mean any data

`*` means any data




 



}
