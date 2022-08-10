EAN Search
----------

A library for EAN, UPC and ISBN name lookup and validation using the API on ean-search.org.

### Install

```
composer require lsv/ean-search
```

### Setup

To use it, you need an API access token from [ean-search](https://www.ean-search.org/ean-database-api.html)

Then you need to set your token with

```php
\Lsv\EanSearch\Request::setApiToken('<token>');
```

### Usage

Everywhere `<language>` is a language from `\Lsv\EanSearch\Utils\LanguageEnum`

Fx. 

```php
\Lsv\EanSearch\Utils\LanguageEnum::SPANISH
```

##### Barcode lookup

Query the ean-search database for a specific barcode.

```php
$response = \Lsv\EanSearch\BarcodeLookup::request('<barcode>', '<language>');
// Response is a \Lsv\EanSearch\Model\ProductModel object
```

##### Barcode prefix search

Query the ean-search database for all barcodes with the same beginning.

```php
$response = \Lsv\EanSearch\BarcodePrefixSearch::request('<barcode prefix>', '<language>');
// Response is an array of \Lsv\EanSearch\Model\ProductModel objects
```

##### Verify checksum

You can verify the checksum in a barcode. This allows you to check if it is a valid barcode regardless if ean-search have it the database or not.

```php
$response = \Lsv\EanSearch\VerifyChecksum::request('<barcode>');
// Response is a \Lsv\EanSearch\Model\VerifyChecksumModel object
```

##### Barcode image

```php
$response = \Lsv\EanSearch\BarcodeImage::request('<barcode>', '<width>', '<height>');
// <width> and <height> is optional
// Response is a \Lsv\EanSearch\Model\BarcodeImageModel object
```

##### Issuing country lookup

Query ean-search database for an issuing country of a barcode.

In contrast to barcode-lookup, this will give a result, even if we don't know the product name.

```php
$response = \Lsv\EanSearch\IssuingCountryLookup::request('<barcode>');
// Response is a \Lsv\EanSearch\Model\IssuingCountryModel object
```

##### Product search

Query the ean-search database for a keyword or product name.

```php
$response = \Lsv\EanSearch\ProductSearch::request('<product name>', '<language>');
// Response is an array of \Lsv\EanSearch\Model\ProductModel objects
```

##### Category search

Query the ean-search database for products from a certain category.

To get a `<category>` you must use  `\Lsv\EanSearch\Utils\CategoryEnum`

Fx.

```php
\Lsv\EanSearch\Utils\CategoryEnum::ART
```

```php
$response = \Lsv\EanSearch\CategorySearch::request('<category>', '<product name>', '<language>');
// <product name> is optional
// Response is an array of \Lsv\EanSearch\Model\ProductModel objects
```

##### Account status

Query the status of your account.

```php
$response = \Lsv\EanSearch\AccountStatus::request();
// Response is a \Lsv\EanSearch\Model\AccountStatusModel object
```

### License

MIT License

Copyright (c) 2022 Martin Aarhof

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.