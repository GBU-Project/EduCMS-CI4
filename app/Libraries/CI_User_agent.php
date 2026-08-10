<?php

/**
 * CI3-compatible User Agent wrapper over the CI4 HTTP UserAgent class.
 */
#[AllowDynamicProperties]
class CI_User_agent
{
    /** @var \CodeIgniter\HTTP\UserAgent */
    protected $ua;

    public function __construct()
    {
        $this->ua = new \CodeIgniter\HTTP\UserAgent();
    }

    public function browser()
    {
        $browser = $this->ua->getBrowser();

        return $browser !== '' ? $browser : false;
    }

    public function version()
    {
        $version = $this->ua->getVersion();

        return $version !== '' ? $version : false;
    }

    public function platform()
    {
        $platform = $this->ua->getPlatform();

        return $platform !== '' ? $platform : false;
    }

    public function agent_string()
    {
        return $this->ua->getAgentString();
    }

    public function is_browser($key = null)
    {
        return $this->ua->isBrowser($key);
    }

    public function is_robot($key = null)
    {
        return $this->ua->isRobot($key);
    }

    public function is_mobile()
    {
        return $this->ua->isMobile();
    }

    public function referrer()
    {
        return $this->ua->getReferrer();
    }
}
