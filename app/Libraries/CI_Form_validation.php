<?php

/**
 * CI3-compatible Form Validation wrapper over the CI4 Validation service.
 */
#[AllowDynamicProperties]
class CI_Form_validation
{
    /** @var \CodeIgniter\Validation\Validation */
    protected $validation;

    /** @var array */
    protected $custom_errors = [];

    public function __construct()
    {
        $this->validation = service('validation');
        $this->validation->reset();
        $this->validation->withRequest(service('request'));
    }

    public function set_rules($group, $label = '', $rules = '')
    {
        if (is_array($group)) {
            $rulesArray = [];
            foreach ($group as $rule) {
                if (! is_array($rule)) {
                    continue;
                }
                $field = $rule['field'] ?? null;
                if ($field === null) {
                    continue;
                }
                $rulesArray[$field] = [
                    'label' => $rule['label'] ?? $field,
                    'rules' => $rule['rules'] ?? '',
                ];
            }
            if (! empty($rulesArray)) {
                $this->validation->setRules($rulesArray, $this->custom_errors);
            }
        } else {
            $this->validation->setRule($group, $label, $rules);
        }

        return $this;
    }

    public function set_message($lang, $value = '')
    {
        if (is_array($lang)) {
            $this->custom_errors = array_merge($this->custom_errors, $lang);
        } else {
            $this->custom_errors[$lang] = $value;
        }

        return $this;
    }

    public function set_data($data = [])
    {
        $this->validation->setData($data);

        return $this;
    }

    public function run($group = null)
    {
        // CI3 semantics: validation only runs on POST/PUT/PATCH/DELETE
        // submissions; on GET the rules are not evaluated (returns FALSE).
        $method = strtoupper((string) service('request')->getMethod());

        if (! in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        return $this->validation->run(null, $group);
    }

    public function error($field, $prefix = '', $suffix = '')
    {
        $error = $this->validation->getError($field);
        if ($error === null || $error === '') {
            return '';
        }

        return $prefix . $error . $suffix;
    }

    public function error_array()
    {
        return $this->validation->getErrors();
    }

    public function error_string($prefix = '', $suffix = '')
    {
        $errors = $this->validation->getErrors();
        $out = '';
        foreach ($errors as $error) {
            $out .= $prefix . $error . $suffix;
        }

        return $out;
    }
}
