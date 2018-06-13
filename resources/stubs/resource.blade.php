{!!$phpOpenTag!!}

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
@foreach($schema as $field)
    @if ($field->isMultipleRelation())
            '{{ $field->name }}' => {{ $field->relationResourceClass }}::collection($this->{{ $field->name }}),
    @else
			'{{ $field->name }}' => $this->{{ $field->name }},
    @endif
@endforeach
		];
    }
}
