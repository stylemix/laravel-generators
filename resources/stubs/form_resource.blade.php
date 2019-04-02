{!! $phpOpenTag !!}

namespace App\Http\Forms;

use Stylemix\Base\Fields\Email;
use Stylemix\Base\Fields\Input;
use Stylemix\Base\Fields\Textarea;
use Stylemix\Base\FormResource;

class {{ $class }} extends FormResource
{

	/**
	 * @return \Stylemix\Base\Fields\Base[]
	 */
	public function fields()
	{
		return [
			// TODO: Declare form fields here
		];
	}
}
