# Templates-PDF

Package for generating PDFs from configurable Templates.

[![swagger](https://img.shields.io/badge/documentation-swagger-green)](https://escolalms.github.io/Templates-PDF/)
[![codecov](https://codecov.io/gh/EscolaLMS/Templates-PDF/branch/main/graph/badge.svg?token=O91FHNKI6R)](https://codecov.io/gh/EscolaLMS/Templates-PDF)
[![Tests PHPUnit in environments](https://github.com/EscolaLMS/Templates-PDF/actions/workflows/test.yml/badge.svg)](https://github.com/EscolaLMS/Templates-PDF/actions/workflows/test.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/60eb83351d2d550c15cb/maintainability)](https://codeclimate.com/github/EscolaLMS/Templates-PDF/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/60eb83351d2d550c15cb/test_coverage)](https://codeclimate.com/github/EscolaLMS/Templates-PDF/test_coverage)
[![downloads](https://img.shields.io/packagist/dt/escolalms/templates-pdf)](https://packagist.org/packages/escolalms/templates-pdf)
[![downloads](https://img.shields.io/packagist/v/escolalms/templates-pdf)](https://packagist.org/packages/escolalms/templates-pdf)
[![downloads](https://img.shields.io/packagist/l/escolalms/templates-pdf)](https://packagist.org/packages/escolalms/templates-pdf)

## Purpose

This package allows you to create PDFs generated after a specific Event is emitted in Laravel / LMS app.

Each PDF Template has a corresponding class describing available variables that can be used in the Template (which will be stored in database and editable through admin panel).
Templates are saved as serialized fabric.js canvas containing these variables which will be replaced during PDF generation with correct data extracted from Event.

Class describing Template Variables must be registered using Template facade from `EscolaLms\Template` package, where you specify which Event it is associated with it and which Channel it is sent through (e.g. `EscolaLms\TemplatesPdf\Core\PdfChannel` which is defined in this package).

## Installing

- `composer require escolalms/templates-pdf`
- `php artisan db:migrate`
- `php artisan db:seed --class="EscolaLms\TemplatesPdf\Database\Seeders\TemplatesPdfSeeder"` to create default templates for all Variable/Event pairs registered for PDF channel

## Dependencies

- `EscolaLms\Templates` core Templates package
- optional: `EscolaLms\Courses` for generating PDFs related to Courses

## Usage

### Defining Templates

1. Create Event which triggers generation of PDF using specified template. This event must implement method `getUser()` returning User model from LMS Core package.
2. Create class defining template Variables, which you will use in PDF template,
3. Associate your class describing template Variables with correct Event and Channel. Use `EscolaLms\Templates\Facades\Template::register(Event class, EscolaLms\TemplatesPdf\Core\PdfChannel::class, Variable class);` method.
4. Use admin panel or `/api/admin/templates` web API to create/edit templates associated with this Variable/Event/Channel set. See [Template package](https://github.com/EscolaLMS/Templates) for more information.

## Tests

Run `./vendor/bin/phpunit --filter 'EscolaLms\\TemplatesPdf\\Tests'` to run tests. See [tests](tests) folder as it contains a basic implementation of Variables class (description of what Template can/must contain) with minimal customisation - a quite good starting point for creating your own.

Test details:
[![Maintainability](https://api.codeclimate.com/v1/badges/60eb83351d2d550c15cb/maintainability)](https://codeclimate.com/github/EscolaLMS/Templates-PDF/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/60eb83351d2d550c15cb/test_coverage)](https://codeclimate.com/github/EscolaLMS/Templates-PDF/test_coverage)

## Usage on front end

### Admin panel

#### **Left menu**

![Menu](docs/menu.png "Menu")

#### **List of templates**

![List of templates](docs/list.png "List of templates")

#### **Creating/editing template**

![Creating/editing template](docs/edit.png "Creating or editing template")

## Permissions

Permissions are defined in [Enum](src/Enums/PdfPermissionsEnum.php) and seeded in [Seeder](database/seeders/PermissionTableSeeder.php).

## Roadmap. Todo. Troubleshooting

- ???
