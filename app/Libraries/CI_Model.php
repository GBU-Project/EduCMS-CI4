<?php

/**
 * CI3-compatible Model base.
 */
#[AllowDynamicProperties]
class CI_Model
{
    public function __construct()
    {
    }

    public function __get($key)
    {
        $instance = Compat::instance();

        if ($instance !== null && isset($instance->{$key})) {
            return $instance->{$key};
        }

        // Lazily load known components through the controller.
        if ($instance !== null && isset($instance->load)) {
            $map = [
                'encryption'     => 'encryption',
                'form_validation' => 'form_validation',
                'agent'          => 'user_agent',
                'rbac'           => 'rbac',
                'logger'         => 'logger',
            ];
            if (isset($map[$key])) {
                try {
                    $instance->load->library($map[$key], null, $key);
                    return $instance->{$key};
                } catch (\Throwable $e) {
                    return null;
                }
            }
        }

        return null;
    }

    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }
}
