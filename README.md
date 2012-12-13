# I18n

The I18n (shorthand for _internationalization_) package provides an easy-to-use and extensible
framework for translating your application or for providing multi-language support in your
application. The framework uses the conventions defined by the Unicode Consortium.





## Requirements

The package requires PHP 5.3 or later. The following packages are required:
[icanboogie/common](https://packagist.org/packages/icanboogie/common) and
[icanboogie/prototype](https://packagist.org/packages/icanboogie/prototype).





## Installation

The recommended way to install this package is through [composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

```json
{
    "minimum-stability": "dev",
    "require": {
		"icanboogie/i18n": "dev-master"
    }
}
```





### Cloning the repository

The package is [available on GitHub](https://github.com/ICanBoogie/I18n), its repository can be
cloned with the following command line:

	$ git clone git://github.com/ICanBoogie/I18n.git





## Documentation

The package is documented as part of the [ICanBoogie](http://icanboogie.org/) framework
[documentation](http://icanboogie.org/docs/). You can generate the documentation for the package
and its dependencies with the `make doc` command. The documentation is generated in the `docs`
directory. [ApiGen](http://apigen.org/) is required. You can later clean the directory with
the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all dependencies required to run the suite. You can later
clean the directory with the `make clean` command.





## License

ICanBoogie/I18n is licensed under the New BSD License - See the LICENSE file for details.