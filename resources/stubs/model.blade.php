{!! $phpOpenTag !!}

namespace {{ $namespace }};

use Illuminate\Database\Eloquent\Model;
@if ($softDeletes)
use Illuminate\Database\Eloquent\SoftDeletes;
@endif

/**
 * Class {{ $class }}
 *
 * @property integer $id
 *
 * @mixin \Eloquent
 */
class {{ $class }} extends Model
{
@if ($softDeletes)
    use SoftDeletes;
@endif

    protected $table = '{{ $table }}';

@foreach ($relations as $relation)
    public function {{ $relation->name }}()
    {
        {!! $relation->relationCode !!}
    }

@endforeach
}
