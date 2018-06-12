## Schema API

Syntax for `--schema` option:
```
--schema="field1:type(argument1,argument2,...):option1(arg1,arg2,...):option2(arg1,arg2,...):..."
```

`type` - can be any supported table schema type


Example:
```
--schema="username:string, email:string(128):unique:form(email)"
```

will generate for migration
```php
$table->string('username');
$table->string('email', 128)->unique();
```

`form(email)` is reserved option for frontend stubs. Can be used to generate form inputs.

#### For stubs
`$schema` variable is available in stubs in following format:
```php
Array
(
    [0] => Array
        (
            [name] => field1
            [type] => type
            [arguments] => Array
                (
                    [0] => argument1
                    [1] => argument2
                )

            [options] => Array
                (
                    [option1] => arg1,arg2
                    [option2] => arg1,arg2
                    ...
                )

        )
	...
```


### Defining relations

Use `relation` as type parameter in following format:
```
RELATION_NAME:RELATION_TYPE(RESOURCE,...ARGUMENTS):form(INPUT_TYPE,...EXTRA_ARGS)
```

`RESOURCE,...ARGUMENTS` and `form` sections are optional.
Meaning of the `ARGUMENTS` are the same as the model relation declaration in [Laravel](https://laravel.com/docs/5.6/eloquent-relationships).

Examples:
```
--schema="user:belongsTo:foreign:form(select,first_name)"
```

in migration:
```php
$table->unsignedInteger('user_id');
$table->foreign('user_id')->references('id')->on('users');
```

in model:
```php
function user() {
  return $this->belongsTo(\App\User::class);
}
```
`user_id` foreign key in migration and `\App\User::class` in model are automatically resolved according to `RELATION_NAME`.

To customize related model and foreign key name pass additional arguments:
```
--schema="user:belongsTo(user,user_id):foreign:form(select,first_name)"
```

Other relations:
```
--schema="phone:hasOne"

--schema="comments:hasMany(comment,foreign_key,local_key)"

--schema="roles:belongsToMany(role)"
```

Many To Many (`belongsToMany`) relation will generate pivot table.
Pivot table name will be `role_user` with `role_id` and `user_id` fields if not provided.

To customize the name of the pivot table and column names pass additional arguments:
```
--schema="roles:belongsToMany(role,user_role,user_id,role_id)"
```

#### For stubs

Use `$relations` variable in stubs as [Laravel Collection](https://laravel.com/docs/5.6/collections).
Each item is instance of RelationItem class.

In schema:
```
--schema="user:belongsTo, comments:hasMany(comment,post_id,id), roles:belongsToMany(role,user_role,user_id,role_id)"
```

```php
foreach ($relations as $name => $relation) {
	// $relation - object instance of RelationItem
	// $name - relation name: (user, comments)
}

$relations['user']->type // belongsTo
$relations['user']->class // \App\User
$relations['user']->foreignKey // "user_id"

$relations['comments']->typeOneOf('hasMany', 'hasOne') // true
$relations['comments']->foreignKey // "post_id"
$relations['comments']->localKey // "id"

$relations['roles']->typeOneOf('belongsToMany') // true
$relations['roles']->pivotTable // "user_role"
$relations['roles']->foreignPivotKey // "user_id"
$relations['roles']->relatedPivotKey // "role_id"
```