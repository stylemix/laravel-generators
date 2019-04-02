{!!$phpOpenTag!!}

namespace {{$namespace}};

use {{$rootNamespace}}{{$model}};
use {{ $rootNamespace }}Http\Forms\{{$model}}Form;
use {{ $rootNamespace }}Http\Requests\{{$model}}Request;
use {{$resourceClassNamespace}}\{{$model}}Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class {{$class}} extends Controller
{

	/**
	 * Display a listing of {{$resource}}.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function index(Request $request)
	{
		$this->authorize('manage', {{$model}}::class);

		return {{$model}}Resource::collection({{$model}}::paginate());
	}

	/**
	 * Creation form for {{$resource}} resource
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function create(Request $request)
	{
		$this->authorize('create', {{$model}}::class);

		return {{$model}}Form::make();
	}

	/**
	 * Store a newly created {{$resource}} in storage.
	 *
	 * @param \{{ $rootNamespace }}Http\Requests\{{$model}}Request $request
	 *
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function store({{$model}}Request $request)
	{
		$this->authorize('create', {{$model}}::class);

 	 	${{$resource}} = {{$model}}::create($request->input());

 	 	return {{$model}}Resource::make(${{$resource}});
	}

	/**
	 * Display the specified {{$resource}}.
	 *
	 * @param \{{ $rootNamespace }}{{$model}} $country
	 *
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function show({{$model}} ${{$resource}})
	{
		$this->authorize('view', ${{$resource}});

		return {{$model}}Resource::make(${{$resource}});
	}

	/**
	 * Edit form for {{$resource}} resource
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \{{ $rootNamespace }}{{$model}} ${{$resource}}
	 *
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function edit(Request $request, {{$model}} ${{$resource}})
	{
		$this->authorize('update', ${{$resource}});

		return {{$model}}Form::make(${{$resource}});
	}

	/**
	 * Update the specified {{$resource}} in storage.
	 *
	 * @param \{{ $rootNamespace }}Http\Requests\{{$model}}Request $request
	 * @param \{{ $rootNamespace }}{{$model}} $country
	 *
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
 	public function update({{$model}}Request $request, {{$model}} ${{$resource}})
	{
		$this->authorize('update', ${{$resource}});

 	 	${{$resource}}->update($request->input());

 	 	return {{$model}}Resource::make(${{$resource}});
	}

	/**
	 * Remove the specified {{$resource}} from storage.
	 *
	 * @param \{{ $rootNamespace }}{{$model}} $country
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function destroy({{$model}} ${{$resource}})
	{
		$this->authorize('delete', ${{$resource}});

 	 	${{$resource}}->delete();

 	 	return Response::json([], 202);
	}
}
