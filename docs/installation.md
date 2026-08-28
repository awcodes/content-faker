---
title: Installation
description: Install Content Faker and publish its config file.
---

# Installation

```bash
composer require awcodes/content-faker
```

The service provider is auto-discovered, so there is nothing to register.

To change the defaults — inline formatting frequency, alert types, merge tag names, CSS class names — publish the config:

```bash
php artisan vendor:publish --tag="content-faker-config"
```

Publishing is optional; without it the package uses its built-in defaults. See [Configuration](usage.md#configuration) for what each key does.
