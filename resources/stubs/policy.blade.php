{!! $phpOpenTag !!}

namespace App\Policies;

use {{ $rootNamespace }}User;
use {{ $rootNamespace }}{{ $model }};
use Illuminate\Auth\Access\HandlesAuthorization;

class {{ $class }}
{

	use HandlesAuthorization;

	/**
	 * Determine whether the user can view the {{ $resource }}.
	 *
	 * @param  \{{ $rootNamespace }}User $user
	 * @param  \{{ $rootNamespace }}{{ $model }} ${{ $resource }}
	 *
	 * @return mixed
	 */
	public function view(User $user, {{ $model }} ${{ $resource }})
	{
		//
	}

	/**
	 * Determine whether the user can create {{ $collection }}.
	 *
	 * @param  \{{ $rootNamespace }}User $user
	 *
	 * @return mixed
	 */
	public function create(User $user)
	{
		//
	}

	/**
	 * Determine whether the user can update the {{ $resource }}.
	 *
	 * @param  \{{ $rootNamespace }}User $user
	 * @param  \{{ $rootNamespace }}{{ $model }} ${{ $resource }}
	 *
	 * @return mixed
	 */
	public function update(User $user, {{ $model }} ${{ $resource }})
	{
		//
	}

	/**
	 * Determine whether the user can delete the {{ $resource }}.
	 *
	 * @param  \{{ $rootNamespace }}User $user
	 * @param  \{{ $rootNamespace }}{{ $model }} ${{ $resource }}
	 *
	 * @return mixed
	 */
	public function delete(User $user, {{ $model }} ${{ $resource }})
	{
		//
	}

	/**
	 * Determine whether the user can restore the {{ $resource }}.
	 *
	 * @param  \{{ $rootNamespace }}User $user
	 * @param  \{{ $rootNamespace }}{{ $model }} ${{ $resource }}
	 *
	 * @return mixed
	 */
	public function restore(User $user, {{ $model }} ${{ $resource }})
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the {{ $resource }}.
	 *
	 * @param  \{{ $rootNamespace }}User $user
	 * @param  \{{ $rootNamespace }}{{ $model }} ${{ $resource }}
	 *
	 * @return mixed
	 */
	public function forceDelete(User $user, {{ $model }} ${{ $resource }})
	{
		//
	}
}
