<?php

$liste_alias = [
    "fzed51\\Core\\Config" => "\Config",
    "fzed51\\Core\\Box" => "\Box",
    "fzed51\\Core\\Route" => "\Route",
    "fzed51\\Core\\Vue" => "\Vue"
];

foreach ($liste_alias as $class => $alias) {
    if (class_exists($class)) {
        class_alias($class, $alias, false);
    }
}
