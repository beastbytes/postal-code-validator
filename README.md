# Postal Code Validator (postal-code-validator)
Provides validation for postal codes.

Postal codes can be validated against a single country or a list of countries.

A postalCodeDataInterface implementation is also required, e.g. beastbytes/postal-code-data-php

**NOTE:** postal-code-validator does _**not**_ guarantee that a postal code exists only that it is in a valid format. For some countries that is - for example - it has the right number of digits, characters in the right place etc. However, for many the validator also checks that disallowed characters are not used, number ranges are correct, etc. If you can improve validation for a country please submit a pull request.

For license information see the [LICENSE](LICENSE.md) file.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist beastbytes/postal-code-validator
```

or add

```json
"beastbytes/postal-code-validator": "^1.0.0"
```

to the "require" section of your composer.json.
