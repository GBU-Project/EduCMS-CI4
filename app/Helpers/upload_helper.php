<?php
if (!function_exists('_upload_helper_verify_real_mime')) {
    /**
     * Cross-check the file's real content type (via fileinfo, reading the
     * actual bytes) against the extensions the caller allows. Returns TRUE
     * if acceptable, or a user-facing error string otherwise.
     */
    function _upload_helper_verify_real_mime($temp_path, $allowed_types) {
        if (!function_exists('finfo_open')) {
            // fileinfo should always be available on modern PHP, but fail
            // closed (reject) rather than silently skip the check if it's
            // somehow missing, since that's exactly the gap being fixed.
            return 'Modul fileinfo tidak tersedia di server; unggahan tidak dapat diverifikasi.';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = $finfo ? finfo_file($finfo, $temp_path) : FALSE;
        if ($finfo) {
            finfo_close($finfo);
        }
        if ($real_mime === FALSE) {
            return 'Gagal mendeteksi tipe file yang sebenarnya.';
        }

        // Allowlist of real MIME types per extension. Only extensions
        // relevant to this app's uploaders are listed; anything not
        // listed here is rejected even if it happens to be in
        // $allowed_types, closing the extension-only validation gap.
        $expected_mimes = array(
            'jpg'  => array('image/jpeg'),
            'jpeg' => array('image/jpeg'),
            'png'  => array('image/png'),
            'gif'  => array('image/gif'),
            'webp' => array('image/webp'),
            'pdf'  => array('application/pdf'),
            'docx' => array('application/zip', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'xlsx' => array('application/zip', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'zip'  => array('application/zip'),
        );

        $extensions = array_filter(array_map('trim', explode('|', strtolower($allowed_types))));
        $matched = FALSE;
        $known_extension = FALSE;

        foreach ($extensions as $ext) {
            if (!isset($expected_mimes[$ext])) {
                continue; // extension not in our allowlist map — never matches
            }
            $known_extension = TRUE;
            if (in_array($real_mime, $expected_mimes[$ext], TRUE)) {
                $matched = TRUE;
                break;
            }
        }

        if (!$known_extension) {
            // Caller allowed an extension this helper doesn't recognize —
            // fail closed rather than silently trust it.
            return 'Tipe file tidak dikenali oleh sistem verifikasi keamanan.';
        }

        if (!$matched) {
            return 'File ditolak: isi file tidak sesuai dengan ekstensinya (terdeteksi sebagai ' . esc_html($real_mime) . ').';
        }

        return TRUE;
    }
}

if (!function_exists('_upload_helper_ensure_no_execute_htaccess')) {
    /**
     * Drop a .htaccess into the target upload directory that disables PHP
     * (and other) script execution, so even if a malicious file somehow
     * lands there, requesting it directly can't run server-side code.
     * No-op on servers that don't use Apache, but harmless either way.
     */
    function _upload_helper_ensure_no_execute_htaccess($dir) {
        $htaccess = rtrim($dir, '/') . '/.htaccess';
        if (file_exists($htaccess)) {
            return;
        }
        $contents = "# Auto-generated: uploaded files must never be executed as scripts.\n"
            . "<IfModule mod_php.c>\n    php_flag engine off\n</IfModule>\n"
            . "<IfModule mod_php7.c>\n    php_flag engine off\n</IfModule>\n"
            . "<FilesMatch \"\\.(php|php3|php4|php5|phtml|pl|py|cgi|asp|aspx|sh|exe)$\">\n"
            . "    Require all denied\n"
            . "</FilesMatch>\n";
        @file_put_contents($htaccess, $contents);
    }
}

if (!function_exists('upload_media')) {
    /**
     * Upload files securely and register them inside the Media Library
     */
    function upload_media($field_name, $subfolder = 'temporary', $allowed_types = 'jpg|jpeg|png|gif|pdf|docx|xlsx|zip', $max_size = 5120) {
        $CI = get_instance();
        
        if (!isset($_FILES[$field_name]) || empty($_FILES[$field_name]['name'])) {
            return array('status' => FALSE, 'error' => 'Tidak ada file yang dipilih untuk diunggah.');
        }

        $temp_path = $_FILES[$field_name]['tmp_name'];

        // 1. Calculate File Integrity Checksum (SHA-256)
        $checksum = hash_file('sha256', $temp_path);

        // 2. Prevent Redundant Physical Uploads (Compare Checksum)
        if ($CI->db->table_exists('media_library')) {
            $CI->db->where('checksum', $checksum);
            $CI->db->where('deleted_at', NULL);
            $existing = $CI->db->get('media_library')->row();

            if ($existing) {
                // Verify physical file exists on server
                $physical_path = FCPATH . $existing->directory . '/' . $existing->disk_name;
                if (file_exists($physical_path)) {
                    return array(
                        'status'    => TRUE,
                        'file_name' => $existing->filename,
                        'disk_name' => $existing->disk_name,
                        'file_path' => $existing->directory . '/' . $existing->disk_name,
                        'mime_type' => $existing->mime_type,
                        'file_size' => $existing->size,
                        'media_id'  => $existing->id
                    );
                }
            }
        }

        // 2b. SECURITY (audit finding #7): validate the file's *actual*
        // content type, not just its extension/client-reported MIME.
        // Extension-only checks let something like shell.php.jpg or an
        // SVG containing <script> through if the web server config isn't
        // airtight. This rejects any upload whose real content doesn't
        // match one of the extensions the caller explicitly allowed.
        $mime_check = _upload_helper_verify_real_mime($temp_path, $allowed_types);
        if ($mime_check !== TRUE) {
            return array('status' => FALSE, 'error' => $mime_check);
        }

        // 3. Process New File Upload
        $subfolder = trim($subfolder, '/');
        $target_dir = './uploads/' . $subfolder;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, TRUE);
        }
        _upload_helper_ensure_no_execute_htaccess($target_dir);

        $original_name = $_FILES[$field_name]['name'];

        // Browsers/OSes (very commonly on Windows, with filenames that
        // went through WhatsApp/Google Drive/renaming with special
        // characters) can send a filename that is NOT valid UTF-8. If that
        // raw string is later passed to json_encode() (e.g. building the
        // JSON response for the uploader, or the media picker), json_encode
        // silently returns false for the WHOLE payload — no exception, no
        // log entry, just an empty response body — which is exactly the
        // "upload gagal" symptom with no error message. Force it to valid
        // UTF-8 here, once, at the source, so every downstream consumer
        // (JSON response, DB insert) is guaranteed a safe string.
        if (!mb_check_encoding($original_name, 'UTF-8')) {
            $original_name = mb_convert_encoding($original_name, 'UTF-8', 'UTF-8');
        }
        // Belt-and-suspenders: mb_convert_encoding('UTF-8','UTF-8') on an
        // already-broken string can still leave invalid sequences on some
        // PHP builds. Strip anything that still isn't valid UTF-8.
        $original_name = htmlspecialchars_decode(htmlspecialchars($original_name, ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8'));

        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        // Generate a random UUID/hash disk name
        $disk_name = md5(uniqid(rand(), TRUE)) . '.' . strtolower($ext);

        $config['upload_path']   = $target_dir;
        $config['allowed_types'] = $allowed_types;
        $config['max_size']      = $max_size;
        $config['file_name']     = $disk_name;

        $CI->load->library('upload');
        $CI->upload->initialize($config);

        if (!$CI->upload->do_upload($field_name)) {
            return array('status' => FALSE, 'error' => $CI->upload->display_errors('', ''));
        }

        $upload_data = $CI->upload->data();

        // Check image resolutions if file is an image
        $width = NULL;
        $height = NULL;
        if (strpos($upload_data['file_type'], 'image') !== FALSE) {
            $width  = $upload_data['image_width'];
            $height = $upload_data['image_height'];
        }

        // 4. Insert Registry Entry into media_library
        $media_id = 0;
        $directory_path = 'uploads/' . $subfolder;
        
        $media_data = array(
            'filename'    => $original_name,
            'disk_name'   => $disk_name,
            'directory'   => $directory_path,
            'extension'   => strtolower($ext),
            'mime_type'   => $upload_data['file_type'],
            'width'       => $width,
            'height'      => $height,
            'size'        => $upload_data['file_size'] * 1024, // Convert KB to Bytes
            'checksum'    => $checksum,
            'uploaded_by' => $CI->session->userdata('user_id'),
            'created_at'  => date('Y-m-d H:i:s')
        );

        if ($CI->db->table_exists('media_library')) {
            $CI->db->insert('media_library', $media_data);
            $media_id = $CI->db->insert_id();
        }

        return array(
            'status'    => TRUE,
            'file_name' => $original_name,
            'disk_name' => $disk_name,
            'file_path' => $directory_path . '/' . $disk_name,
            'mime_type' => $upload_data['file_type'],
            'file_size' => $upload_data['file_size'] * 1024,
            'media_id'  => $media_id
        );
    }
}
