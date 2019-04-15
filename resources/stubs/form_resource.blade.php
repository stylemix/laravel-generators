{!! $phpOpenTag !!}

namespace App\Http\Forms;

use Stylemix\Base\Fields\CheckboxField;
use Stylemix\Base\Fields\CheckboxesField;
use Stylemix\Base\Fields\EmailField;
use Stylemix\Base\Fields\NumberField;
use Stylemix\Base\Fields\PasswordField;
use Stylemix\Base\Fields\SelectField;
use Stylemix\Base\Fields\TextField;
use Stylemix\Base\Fields\TextareaField;
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
