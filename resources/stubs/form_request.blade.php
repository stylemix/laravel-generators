{!! $phpOpenTag !!}

namespace App\Http\Requests;

use App\Http\Forms\{{ str_replace('Request', '', $class) . 'Form' }};
use Stylemix\Base\FormRequest;

class {{ $class }} extends FormRequest
{

	/**
	 * @return \Stylemix\Base\FormResource
	 */
	protected function formResource()
	{
		return {{ str_replace('Request', '', $class) . 'Form' }}::make();
	}
}
