# Content Faker

Generate realistic fake **Markdown**, **HTML**, and **rich editor** content for Laravel model factories, database seeders, rich text previews, documentation examples, renderer tests, CMS demos, and UI screenshots.

## Documentation

The full documentation lives at **[docs.aw.codes/content-faker](https://docs.aw.codes/content-faker/1.x)**.

## Requirements

- PHP 8.3 or later
- `fakerphp/faker` and `illuminate/support`

It is Laravel-friendly and works outside a full Laravel application where practical.

## Installation

```bash
composer require awcodes/content-faker
```

The service provider is auto-discovered; see [Installation](https://docs.aw.codes/content-faker/1.x/installation) if you want to publish the optional config.

## Changelog

Please see the [releases](https://github.com/awcodes/content-faker/releases) for what has changed recently.

## Contributing

The repository ships an Orchestra Testbench Workbench under `workbench/` that renders every generator's output, so changes to a faker are visible immediately:

```bash
composer install   # install dependencies
composer test      # run Rector, Pint, Larastan and Pest
composer serve     # build and seed the Workbench, then start it at http://127.0.0.1:8000
```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [awcodes](https://github.com/awcodes)
- [All Contributors](../../contributors)

## License

MIT — see [LICENSE.md](LICENSE.md).
