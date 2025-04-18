# EAV library

[![Latest Version](https://img.shields.io/github/release/kuperwood/eav.svg?style=flat-square)](https://github.com/kuperwood/eav/releases)
[![Build Status](https://github.com/kuperwood/eav/workflows/tests/badge.svg)](https://github.com/kuperwood/eav/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://opensource.org/license/mit)
[![Quality Score](https://img.shields.io/scrutinizer/g/kuperwood/eav.svg?style=flat-square)](https://scrutinizer-ci.com/g/kuperwood/eav)
[![Code Coverage](https://scrutinizer-ci.com/g/kuperwood/eav/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kuperwood/eav/?branch=master)

The EAV engine is designed to manage and manipulate EAV data across multiple domains. Library offers functionalities for configurable entity CRUD, importing and exporting entities, as well as managing attribute sets and groups. The attributes are configurable, and attribute strategies with hooks are available. The approach and concepts are inspired by the "Magento way". The application is specifically tailored for a custom CMS-oriented environment.

### Features
- manage attribute types, attributes, attributes sets and groups, entities
- entity CRUD and validation

### Requirements
- PHP >=7.4
- ext-pdo

[Documentation](./docs/eav.md)<br>
[Examples](./docs/examples.md)

### Installation
```bash
$ composer require kuperwood/eav
```

### License

Eav package is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2025 [Alex Kuperwood](https://github.com/kuperwood)