# Dionysos Font Field
> This component is a part of the **Olympus Dionysos fields** for **WordPress**.

```sh
composer require getolympus/olympus-dionysos-field-font
```

---

[![Olympus Component][olympus-image]][olympus-url]
[![CodeFactor Grade][codefactor-image]][codefactor-url]
[![Packagist Version][packagist-image]][packagist-url]
[![MIT][license-image]][license-blob]

---

<p align="center">
    <img src="https://github.com/GetOlympus/olympus-dionysos-field-font/blob/master/assets/field-font-64.png" />
</p>

---

## Field initialization

Use the following lines to add an `font field` in your **WordPress** admin pages or custom post type meta fields:

```php
return \GetOlympus\Dionysos\Field\Font::build('my_font_field_id', [
    'title'       => 'Select your character!',
    'default'     => [
        'family'         => 'Open Sans',
        'backup'         => 'Arial, Helvetica, sans-serif',
        'weight'         => 200,
        'style'          => 'normal',
        'subsets'        => 'latin',
        'size'           => '16px',
        'line-height'    => '1.8',
        'letter-spacing' => '0',
        'color'          => '#000000',
    ],
    'description' => 'Select your fighting stage!',
]);
```

## Variables definition

| Variable      | Type    | Default value if not set | Accepted values |
| ------------- | ------- | ------------------------ | --------------- |
| `title`       | String  | `'Font'` | *empty* |
| `default`     | Array   | *empty* | *empty* |
| `description` | String  | *empty* | *empty* |

## Retrive data

Retrieve your value from Database with a simple `get_option('my_font_field_id', [])` (see [WordPress reference][getoption-url]).  
Below, a `json_encode()` example to understand how data are stored in Database:

```json
{
  "family": "Open Sans",
  "backup": "Arial, Helvetica, sans-serif",
  "weight": 200,
  "style": "normal",
  "subsets": "latin",
  "size": "16px",
  "line-height": "1.8",
  "letter-spacing": "0",
  "color": "#000000"
}
```

And below, a simple example to show how to iterate on the data array in `PHP`:

```php
// Get font from Database
$font = get_option('my_font_field_id', []);

// Check if font is empty and display it
if (!empty($font)) {
    echo '<h1 style="font-family:'.$font['family'].';font-size:'.$font['size'].'">My custom title</h1>';
}
```

## Release History

0.0.1
- Initial commit

## Contributing

1. Fork it (<https://github.com/GetOlympus/olympus-dionysos-field-font/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request

---

**Built with ♥ by [Achraf Chouk](https://github.com/crewstyle "Achraf Chouk") ~ (c) since a long time.**

<!-- links & imgs dfn's -->
[olympus-image]: https://img.shields.io/badge/for-Olympus-44cc11.svg?style=flat-square
[olympus-url]: https://github.com/GetOlympus
[codefactor-image]: https://www.codefactor.io/repository/github/GetOlympus/olympus-dionysos-field-font/badge?style=flat-square
[codefactor-url]: https://www.codefactor.io/repository/github/getolympus/olympus-dionysos-field-font
[getoption-url]: https://developer.wordpress.org/reference/functions/get_option/
[license-blob]: https://github.com/GetOlympus/olympus-dionysos-field-font/blob/master/LICENSE
[license-image]: https://img.shields.io/badge/license-MIT_License-blue.svg?style=flat-square
[packagist-image]: https://img.shields.io/packagist/v/getolympus/olympus-dionysos-field-font.svg?style=flat-square
[packagist-url]: https://packagist.org/packages/getolympus/olympus-dionysos-field-font