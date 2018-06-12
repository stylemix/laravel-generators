## Extra option API

Syntax for `--extra` option:
```
--extra="feature1:option1(arg1,arg2,...):option2(arg1,arg2,...):..."
```

Use `$extra` variable in stubs as [Laravel Collection](https://laravel.com/docs/5.6/collections).
Each item is instance of ExtraItem class.

Examples:

```php
// search:enabled,sorting
$extra['search'] // object instance of ExtraItem
$extra['search']->option('enabled') // true
$extra->has('sorting') // true
$extra->has('other') // false
isset($extra['other']) // false
$extra['other'] // throws exception

// search:type(scout,base):enabled
echo $extra['search']->optionArg('type', 0) // "scout"
echo $extra['search']->optionArg('type', 1) // "base"
echo $extra['search']->optionArg('enabled', 0, 'default') // "defaut"

```