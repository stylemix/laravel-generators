{!! $phpOpenTag !!}

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class {{ $class }} extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('{{ $table }}', function (Blueprint $table) {
			{!! trim($schema_up) !!}
@if ($softDeletes)
			$table->softDeletes();
@endif
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		{!! trim($schema_down) !!}
	}
}
