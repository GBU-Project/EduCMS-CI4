<?php

use CodeIgniter\HTTP\DownloadResponse;

if (!function_exists('force_download')) {
    function force_download($filename = '', $data = '')
    {
        if ($filename === '' || $data === '') {
            return;
        }

        $response = service('response');

        if ($data === null) {
            $download = new DownloadResponse(basename((string) $filename), true);
            $download->setFilePath((string) $filename);
        } else {
            $download = new DownloadResponse((string) $filename, true);
            $download->setBinary($data);
        }

        $download->send();

        exit;
    }
}
