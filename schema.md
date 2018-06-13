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

Use `$schema` variable in stubs as [Laravel Collection](https://laravel.com/docs/5.6/collections).
Each item is instance of SchemaItem class.

For command:
```
--schema="username:string, email:string(128):unique:form(email)"
```
in stub will be:
```php
foreach ($schema as $name => $field) {
	// $field - object instance of SchemaItem
	// $name - schema item name: (user, comments)
}

$field = $schema['username']
$field->name // username
$field->type // string
$field->typeOneOf('string', 'text') // true

$field = $schema['email']
$field->argument(0) // 128
$field->option('unique') // true
$field->option('unique') // true
$field->option('form') // array("email")
$field->option('form', 0) // "email"
```

For schema with relations:
```
--schema="user:belongsTo, comments:hasMany(comment,post_id,id), roles:belongsToMany(role,user_role,user_id,role_id)"
```

```php
$field = $schema['user']
$field->type // belongsTo
$field->foreignKey // "user_id"
$field->relationClass // "\App\User"
$field->relationCode // "$this->>belongsTo(\App\User::class)"

$field = $schema['comments']
$field->typeOneOf('hasMany', 'hasOne') // true
$field->foreignKey // "post_id"
$field->localKey // "id"

$field = $schema['roles']
$field->typeOneOf('belongsToMany') // true
$field->pivotTable // "user_role"
$field->foreignPivotKey // "user_id"
$field->relatedPivotKey // "role_id"
```

Only for `generate:model` variable `$relations` is available as [Laravel Collection](https://laravel.com/docs/5.6/collections).
It contains schema fields only type of relations.
