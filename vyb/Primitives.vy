informal vy.vyb.Primitives @20240117 {

Vy binary primitives
====================

Binary primitives are the binary items which can appear in any
[Containers](#Containers). 

null
----

$00 : null

It means empty data.

bool
----

$2b: true
$2d: false

Logical values. Can be used as vy.comp.Bool.

int
---

$30: the number 0
$31xx: integer storable in 8 bits (-128 .. 127)
$32xxxx: integer storable in 16 bits (-32768..32767)
..
$38xxxxxxxxxxxxxxxxxx: integer storable in 72 bits

Integer values. Can be used as any "constdecimal" typed data.

string
------

$27(size:int)... : ASCII string
$22(size:int)... : UTF-8 string

   size: number of bytes following

String values. Can be used as any "conststring" typed data. 

reference
---------

$5e(val:int)

Reference value. References an item in a predefined pool.

list
----

$5b(count:int)...

   count: number of elements

A list of values. 



