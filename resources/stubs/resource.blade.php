{!! $phpOpenTag !!}

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class {{ $class }} extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
@foreach($values as $key => $value)
			'{{ $key }}' => {!! $value !!},
@endforeach
		];
    }
}
