<?php namespace Backend\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Carbon\Carbon;

/**
 * RRule
 * Renders RRule fields.
 *
 * @package kurtjensen\mycalendar
 * @author Kurt Jensen
 */
class RRule extends FormWidgetBase
{

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'rrule';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->fillFromConfig([
            'mode',
            'minDate',
            'maxDate',
        ]);

        $this->mode = strtolower($this->mode);

        $this->minDate = is_integer($this->minDate)
        ? Carbon::createFromTimestamp($this->minDate)
        : Carbon::parse($this->minDate);

        $this->maxDate = is_integer($this->maxDate)
        ? Carbon::createFromTimestamp($this->maxDate)
        : Carbon::parse($this->maxDate);
    }

    public function t($string)
    {
        return Lang::get('kurtjensen.mycalendar::lang.rdate.' . $string);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('datepicker');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();

        $this->vars['timeValue'] = null;

        if ($value = $this->getLoadValue()) {

            /*
             * Date / Time
             */
            if ($this->mode == 'datetime') {
                if (is_object($value)) {
                    $value = $value->toDateTimeString();
                }

                $dateTime = explode(' ', $value);
                $value = $dateTime[0];
                $this->vars['timeValue'] = isset($dateTime[1]) ? substr($dateTime[1], 0, 5) : '';
            }
            /*
             * Date
             */
            elseif ($this->mode == 'date') {
                if (is_string($value)) {
                    $value = substr($value, 0, 10);
                } elseif (is_object($value)) {
                    $value = $value->toDateString();
                }
            } elseif ($this->mode == 'time') {
                if (is_object($value)) {
                    $value = $value->toTimeString();
                }
            }

        }

        $this->vars['value'] = $value ?: '';
        $this->vars['field'] = $this->formField;
        $this->vars['mode'] = $this->mode;
        $this->vars['minDate'] = $this->minDate;
        $this->vars['maxDate'] = $this->maxDate;
    }

    /**
     * {@inheritDoc}
     */
    protected function loadAssets()
    {
        $this->addCss('vendor/pikaday/css/pikaday.css', 'core');
        $this->addCss('vendor/clockpicker/css/jquery-clockpicker.css', 'core');
        $this->addCss('css/datepicker.css', 'core');
        $this->addJs('js/build-min.js', 'core');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        if ($this->formField->disabled) {
            return FormField::NO_SAVE_DATA;
        }

        if (!strlen($value)) {
            return null;
        }

        $timeValue = post(self::TIME_PREFIX . $this->formField->getName(false));
        if ($this->mode == 'datetime' && $timeValue) {
            $value .= ' ' . $timeValue . ':00';
        } elseif ($this->mode == 'time') {
            $value = substr($value, 0, 5) . ':00';
        }

        return $value;
    }
}
