# Web Sequence Diagram API Client

[![Build Status](https://travis-ci.org/bmd/wsdiagram.svg?branch=master)](https://travis-ci.org/bmd/wsdiagram)
[![Coverage Status](https://coveralls.io/repos/github/bmd/wsdiagram/badge.svg?branch=master)](https://coveralls.io/github/bmd/wsdiagram?branch=master)

Installation:

```
composer require bmd/websequencediagrams
```

Usage:

```php
<?php

use \WebSequenceDiagrams\Diagram;

$diagram = new Diagram(
    'A->B'
);

$diagram->setStyle('earth')
    ->setFormat('png');

$diagram->render();

$diagram->save('output.png');
```