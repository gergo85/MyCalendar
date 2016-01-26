<?php namespace KurtJensen\MyCalendar\Models;

use Model;
use System\Classes\PluginManager;

/**
 * Settings Model
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'kurtjensen_calendar_settings';

    public $settingsFields = 'fields.yaml';

    public $permOptions = [];

    /**
     * Validation rules
     */
    public $rules = [
        'public_perm' => 'required',
        'deny_perm' => 'required',
        'default_perm' => 'required',
    ];

    public function initSettingsData()
    {
        $this->date_format = 'F jS, Y';
        $this->time_format = 'g:i a';
        $options = array_flip($this->getDropdownOptions());
        $this->public_perm = $this->public_perm ? $this->public_perm : array_get($options, 'calendar_public', 0);
        $this->deny_perm = $this->deny_perm ? $this->deny_perm : array_get($options, 'calendar_deny_all', 0);
        $this->default_perm = $this->default_perm ? $this->default_perm : array_get($options, 'calendar_deny_all', 0);
    }

    public function getDropdownOptions($fieldName = null, $keyValue = null)
    {
        if (count($this->permOptions)) {
            return $this->permOptions;
        }

        $manager = PluginManager::instance();
        if ($manager->exists('shahiemseymor.roles')) {
            $this->permOptions = \ShahiemSeymor\Roles\Models\UserPermission::lists('name', 'id');
        }
        return $this->permOptions;
    }

}
