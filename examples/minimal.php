<?php

require './../vendor/autoload.php';

$diagram = new \WebSequenceDiagrams\Diagram('A->B:');

$diagram->render();
$diagram->save();
