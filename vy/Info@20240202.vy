informal vy.Info @20240202 {

Vy
==

Vy helps you write and distribute software in a form that makes it possible
for a very wide variety of computers to run.

Features which complete that task:

There are some [core](#vy.core) interfaces which are supported in all vy environments.
The representation and implementation of these can differ in different
architectures

Vy defines a [Binary form](#vyb.Info) which can be safely distributed 
on the internet. A binary vy file can contain executable code, resources,
or any other kind of data. Executables can be put in a general (any system)
form, or in a system-specific format.

A vy program runs in a [Context](#Contexts). As computers and systems are
very different, vy needs a way to run a program in a way independent of the
features of the running system. When run, the vy program gets the running 
context as parameter. It then queries the context for the features it needs
(like input, output streams, graphical user interface, audio playing, 
3d graphics), and continues with the services provided by the context. 
This makes it possible to run the same program in various ways (quarter screen,
black and white, logging all internet calls, with larger buttons, etc...)
without having to change the program at all.

The vy system uses [Interfaces](#Interfaces) extensively. As even the basic
types (like int) differ in different systems, Vy only uses the features of 
any type or service which are defined in vy files called interfaces. Interfaces
are very well defined things, written in vy interface files.

All vy objects and sources have a [Version](#Versions). A vy version number 
is an @ followed by the digits of the date of creation. So the 
version of baby Albert Enstein would be @18790314 :) That way versions of
even completely different objects are easily comparable.

Goals
=====

Vy still needs a lot to be finished. There are still [Goals](#Goals), 
to achieve.

}
