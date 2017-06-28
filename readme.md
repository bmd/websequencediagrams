# Web Sequence Diagram API Client

[![Build Status](https://travis-ci.org/bmd/websequencediagrams.svg?branch=master)](https://travis-ci.org/bmd/websequencediagrams)
[![Coverage Status](https://coveralls.io/repos/github/bmd/websequencediagrams/badge.svg?branch=master)](https://coveralls.io/github/bmd/websequencediagrams?branch=master)

### Installation

```
composer require bmd/websequencediagrams
```

### Documentation

You can see the full documentation of the Websequencediagrams API, including the valid values for 
all parameters here: http://www.websequencediagrams.com/embedding.html

### Usage

```php
<?php

use \WebSequenceDiagrams\Diagram;

// you can set all of the properties in the constructor...
$diagram = new Diagram(
    $message = 'A->B:', 
    $style = 'default', 
    $format = 'png',
    // if your diagrams include premium features, you need to set an API key
    $apiKey = 'my-api-key' 
);

// or via setters
$diagram->setMessage('A->B:')
    ->setStyle('earth')
    ->setFormat('png')
    ->setApiKey('my-api-key');

// the render() method makes a request to the web sequence diagrams API and returns
// a URL to the diagram in the requested format.
$diagram->render();

// the created diagram must be retrieved within 2 minutes. If $baseFileName is falsey
// then the md5 hash of the "message" property will be used. You could use this to 
// avoid re-rendering a diagram that you've already saved locally. Be a good API citizen
// and don't make more calls than you need to.
$diagram->save($dir = '.', $baseFileName = 'output');
```