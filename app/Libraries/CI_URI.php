<?php

/**
 * CI3-compatible URI wrapper.
 */
#[AllowDynamicProperties]
class CI_URI
{
    public function uri_string()
    {
        $segments = service('uri')->getSegments();

        return implode('/', $segments);
    }

    public function segment($n, $default = '')
    {
        return service('uri')->getSegment((int) $n, $default);
    }

    public function rsegment($n)
    {
        return $this->segment($n);
    }

    public function rsegments()
    {
        $segments = service('uri')->getSegments();

        $out = [];
        foreach ($segments as $i => $seg) {
            $out[$i + 1] = $seg;
        }

        return $out;
    }

    public function total_segments()
    {
        return count(service('uri')->getSegments());
    }
}
