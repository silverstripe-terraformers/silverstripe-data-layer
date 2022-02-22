# XDebug

For specific set up instructions for PHPStorm, please see our [PHPStorm](phpstorm.md) docs.

Once you setup XDebug, you should be able to enable or disable it.
Please refer to https://silverstripe.atlassian.net/l/c/17B1GV8d for more information.

(Optional) Create a .vm_aliases file from .vm_aliases.dist
```
$ cp .vm_aliases.dist .vm_aliases
```

## Toggling XDebug on and off without aliases

```
# enable xdebug
$ sudo phpenmod xdebug && sudo service apache2 restart

# disabble xdebug
$ sudo phpdismod xdebug && sudo service apache2 restart
```

## Toggling XDebug on and off with aliases

```
# enable xdebug
$ enxdebug

# disable xdebug
$ disxdebug
```
