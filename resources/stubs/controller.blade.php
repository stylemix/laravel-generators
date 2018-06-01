{!!$phpOpenTag!!}

namespace {{$namespace}};

use {{$rootNamespace}}{{$model}};
use Illuminate\Http\Request;
use App\Http\Requests\Create{{$model}}Request;
use App\Http\Requests\Update{{$model}}Request;
use {{$resourceClassNamespace}}\{{$model}} as {{$model}}Resource;

class {{$class}} extends Controller
{

	/**
	 * Display a listing of {{$resource}}.
	 */
	public function index(Request $request)
	{
		return {{$model}}Resource::collection({{$model}}::paginate());
	}

	/**
	 * Store a newly created {{$resource}} in storage.
	 */
	public function store(Create{{$model}}Request $request)
	{
        ${{$resource}} = {{$model}}::create($request->input());

        return new {{$model}}Resource(${{$resource}});
	}

	/**
	 * Display the specified {{$resource}}.
	 */
	public function show({{$model}} ${{$resource}})
	{
		return new {{$model}}Resource(${{$resource}});
	}

	/**
	 * Update the specified {{$resource}} in storage.
     */
    public function update(Update{{$model}}Request $request, {{$model}} ${{$resource}})
	{
        ${{$resource}}->update($request->input());

        return new {{$model}}Resource(${{$resource}});
	}

	/**
	 * Remove the specified {{$resource}} from storage.
	 */
	public function destroy({{$model}} ${{$resource}})
	{
        ${{$resourceLowercase}}->delete();

        return response(['message' => '{{$model}} has been deleted successfully'], 200);
	}
}
