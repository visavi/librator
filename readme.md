# Class text library to read and process text from a file by pages

[![Latest Stable Version](https://poser.pugx.org/visavi/librator/v/stable)](https://packagist.org/packages/visavi/librator)
[![Total Downloads](https://poser.pugx.org/visavi/librator/downloads)](https://packagist.org/packages/visavi/librator)
[![Latest Unstable Version](https://poser.pugx.org/visavi/librator/v/unstable)](https://packagist.org/packages/visavi/librator)
[![License](https://poser.pugx.org/visavi/librator/license)](https://packagist.org/packages/visavi/librator)

Basic useful feature list:

 * Reading text files
 * A breakdown of the text on page
 * Beautiful displays pages, with the ability to jump to the last and first page

### Example of use

```php
<?php
// Open the text file library.txt
$librator = new Visavi\Librator('library.txt');

// The tag line feed, the default <br /> (optional)
$librator->setBreak('<br>');

// The number of output lines
$librator->read(20);
```

### Installing

```
composer require visavi/librator
```

### License

The class is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
