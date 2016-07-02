# Standard Objects for Liturgies Using This Library
Any liturgy program that uses this library should define the following objects in
it's global variable-space as early as possible.

## Super-Available to Language Module Segments
The segments in language-modules are PHP scripts that
run in their own local variable-space where __$thefile__
identifies the file.
However, the following objects will have their
referential variables copied from global-space
to these segments' local-space so that they may
be used there more readily.

### $sm

### $lct

### $strmagic

### $credits
A __deferred\_output__ object that should have it's
contents dumped to standard-output at some point during
the credits section at the end of the document after
the liturgy.

### $lngu
A __language\_tool__ object

## Just For Global Space
There is no real scenario in which any of the language
modules should need to deal directly with any of these
objects (but rather, would only need to deal with them
through the library) - and therefore, their
referential variables are not
copied from global-space to the segments local variable-space.

### $ttlng
A __language\_tool__ object specifically for titles
\- as the search-preferences for titles could be
different than for actual prayer text.

### $footnts
A __deferred\_output__ object that should have it's
contents dumped to standard-output at some point during
the footnotes section at the end of the document after
the liturgy.

