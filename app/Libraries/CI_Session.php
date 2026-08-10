<?php

/**
 * CI3-compatible Session wrapper over the CI4 session service.
 */
#[AllowDynamicProperties]
class CI_Session
{
    /** @var \CodeIgniter\Session\Session */
    protected $session;

    public function __construct()
    {
        $this->session = service('session');
    }

    public function userdata($key = null)
    {
        return $this->session->get($key);
    }

    public function has_userdata($key)
    {
        return $this->session->has($key);
    }

    public function set_userdata($key, $value = null)
    {
        if (is_array($key)) {
            $this->session->set($key);
        } else {
            $this->session->set($key, $value);
        }

        return $this;
    }

    public function unset_userdata($key)
    {
        $this->session->remove($key);

        return $this;
    }

    public function all_userdata()
    {
        return $this->session->get();
    }

    public function set_flashdata($key, $value)
    {
        $this->session->setFlashdata($key, $value);

        return $this;
    }

    public function flashdata($key = null)
    {
        return $this->session->getFlashdata($key);
    }

    public function keep_flashdata($key)
    {
        $this->session->keepFlashdata($key);

        return $this;
    }

    public function sess_destroy()
    {
        $this->session->destroy();
    }

    public function sess_regenerate($destroy = false)
    {
        $this->session->regenerate($destroy);
    }

    public function mark_as_flash($key)
    {
        $this->session->markAsFlashdata($key);

        return $this;
    }

    public function __get($key)
    {
        return $this->userdata($key);
    }

    public function __set($key, $value)
    {
        $this->set_userdata($key, $value);
    }
}
