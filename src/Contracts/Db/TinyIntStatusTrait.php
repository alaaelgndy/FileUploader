<?php
namespace Elgndy\FileUploader\Contracts\Db;

use Carbon\Carbon;

/**
 * TinyInt Based Status Column Helper
 */
trait TinyIntStatusTrait
{
    private $status_list;


    public function getStatusAttribute($value)
    {
        $status_values = array_flip($this->getStatusList());
        return (isset($status_values[$value]) ? $status_values[$value] : null);
    }

    public function setStatusAttribute($value)
    {
        $status_keys = $this->getStatusList();
        $this->attributes['status'] = isset($status_keys[$value]) ? $status_keys[$value] : $this->status;
        $this->checkDateHookup($value);
    }

    public function setStatusStrict($status)
    {
        if ($this->isValidStatus($status) && $this->canBeStatus($status)) {
            $this->setStatusAttribute($status);
            $this->save();
            return true;
        }
        return false;
    }

    public function getStatusValue($status)
    {
        $status_keys = $this->getStatusList();
        return isset($status_keys[$status]) ? $status_keys[$status] : null;
    }

    public function getStatusList()
    {
        if (!isset($this->status_list)) {
            $this->resolveStatusConsts();
        }
        return $this->status_list;
    }

    public function isValidStatus($status)
    {
        return (in_array($status, $this->getStatusList()));
    }

    public function isStatus($status)
    {
        return ($this->status == $status);
    }

    public function isOrStatus(array $statuses)
    {
        foreach ($statuses as $status) {
            if ($status == $this->status) {
                return true;
            }
        }

        return false;
    }

    public function canBeStatus($status)
    {
        return (isset($this->status_can_be) && isset($this->status_can_be[$status]))
                ? in_array($this->status, $this->status_can_be[$status])
                : true;
    }

    private function resolveStatusConsts()
    {
        $reflect_this = new \ReflectionClass($this);
        $consts = $reflect_this->getConstants();
        foreach ($consts as $const => $value) {
            if (strpos(strtolower($const), 'status') !== false) {
                $name = explode('_', $const, 2);
                $name = ucfirst(strtolower($name[1]));
                $this->status_list[$name] = $value;
            }
        }
    }

    private function checkDateHookup($status)
    {
        if (isset($this->status_date_column[$status]) && $column = $this->status_date_column[$status]) {
            $this->attributes[$column] = Carbon::now();
        }
    }
}
