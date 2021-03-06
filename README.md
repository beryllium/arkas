ARKAS the Hunter
================

Arkas the Hunter is a code search tool. It's basically grep with a twist - when 
examining certain files, such as PHP files, it keeps track of the current Class 
and Function being scanned. This helps in the hunt for obscure bugs and 
regressions.

Using Arkas, you can trace spaghetti code back through the entire codebase to 
reveal exactly how deep the rabbit hole goes.

There is a lot of room for code cleanup and optimization in this initial version,
but it works well enough to save me from a headache every once in a while.

Before Installing
-----------------

Compiling arkas.phar requires that phar.readonly be set to "Off" in your php.ini:

    [Phar]
    phar.readonly = Off
  

Installation
------------

Type this on the command line to create the arkas.phar file:

    $ bin/fetch-composer
    $ ./composer.phar install
    $ php compile

Then, symlink to arkas.phar somewhere in your path (assuming you cloned it to /data/tools/arkas):
  
    $ sudo ln -s /data/tools/arkas/arkas.phar /usr/local/bin/arkas


Usage
-----

Change into the folder you want to search:

    $ cd /var/www

Issue a simple search:
  
    $ arkas mysqli_real_escape_string



Contributors
------------

- Kevin Boyd <kboyd@acdsystems.com>

Based on Silex and Cilex, and the Symfony2 components.

- Silex & Symfony2: Fabien Potencier <fabien@symfony.com>
- Cilex: Mike van Riel <mike.vanriel@naenius.com>
