{!! $phpOpenTag !!}

use Faker\Generator as Faker;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

$factory->define({{$rootNamespace}}{{$model}}::class, function (Faker $faker) {
	return [
@foreach ($schema as $name => $field)
		'{{ $field->name }}' => '',
@endforeach
	];
});
