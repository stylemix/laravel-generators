{!!$phpOpenTag!!}

namespace {{$namespace}};

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class {{$class}}
 * @mixin \Eloquent
 */
class {{$class}} extends Model
{
    use SoftDeletes;
@if ($extra->has('search'))
    use Searchable;
    // {{ $extra['search']->option('type') }}
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