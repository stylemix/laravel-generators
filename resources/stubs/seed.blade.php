{!! $phpOpenTag !!}

use Illuminate\Database\Seeder;
use {{ $rootNamespace }}{{ $model }};

class {{ $class }} extends Seeder
{
	public function run(Faker\Generator $faker)
	{
		factory({{ $model }}::class, 3)->create();
	}
}
