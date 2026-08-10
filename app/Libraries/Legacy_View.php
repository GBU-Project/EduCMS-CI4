<?php

use CodeIgniter\View\View;

/**
 * View renderer subclass that exposes the CI3 controller graph to view
 * files via $this->load, $this->session, $this->security, ... exactly the
 * way CI3 views used the active controller.
 */
#[AllowDynamicProperties]
class Legacy_View extends View
{
    public function __get($key)
    {
        $instance = Compat::instance();

        if ($instance !== null) {
            if (isset($instance->{$key})) {
                return $instance->{$key};
            }
            if (isset($instance->load)) {
                $map = [
                    'agent'          => 'user_agent',
                    'form_validation' => 'form_validation',
                    'rbac'           => 'rbac',
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
        }

        return null;
    }

    /**
     * Render an absolute file path (used for active-theme views resolved
     * from FCPATH). Mirrors the include logic of View::render().
     */
    public function renderFile(string $file, ?bool $saveData = null): string
    {
        $saveData ??= $this->saveData;

        if (! is_file($file)) {
            throw \CodeIgniter\View\ViewException::forInvalidFile($file);
        }

        $this->prepareTemplateData($saveData);

        $renderVars = $this->renderVars;

        $output = (function () use ($file): string {
            extract($this->tempData);
            ob_start();
            include $file;

            return ob_get_clean() ?: '';
        })();

        if ($this->layout !== null && $this->sectionStack === []) {
            throw new \RuntimeException('Layouts are not supported for theme view files.');
        }

        return $this->decorateOutput($output);
    }
}
