{!!$phpOpenTag!!}

namespace {{$namespace}};

use Illuminate\Database\Eloquent\Model;
@if ($softDeletes)
use Illuminate\Database\Eloquent\SoftDeletes;
@endif

/**
 * Class {{$class}}
 * @mixin \Eloquent
 */
class {{$class}} extends Model
{
@if ($softDeletes)
    use SoftDeletes;
@endif

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = '{{$table}}';

@foreach ($relations as $relation)
    public function {{ $relation->name }}()
    {
        {!! $relation->relationCode !!}
    }

@endforeach
}
