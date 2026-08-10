<?php

/**
 * CI3-compatible Upload library implemented directly over PHP $_FILES
 * (used only by upload_helper.php).
 */
#[AllowDynamicProperties]
class CI_Upload
{
    protected $upload_path = '';
    protected $allowed_types = '';
    protected $max_size = 0;   // KB
    protected $max_width = 0;
    protected $max_height = 0;
    protected $file_name = '';
    protected $file_ext = '';
    protected $errors = [];

    protected $data = [];

    public function __construct($config = [])
    {
        if (! empty($config)) {
            $this->initialize($config);
        }
    }

    public function initialize($config = [])
    {
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
        $this->errors = [];

        return $this;
    }

    public function do_upload($field = 'userfile')
    {
        $this->errors = [];
        $this->data = [];

        if (! isset($_FILES[$field])) {
            $this->errors[] = 'Tidak ada file yang diunggah.';

            return false;
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'Upload file gagal (kode error ' . $file['error'] . ').';

            return false;
        }

        if ($file['size'] === 0) {
            $this->errors[] = 'File yang diunggah kosong.';

            return false;
        }

        if ($this->max_size > 0 && $file['size'] > $this->max_size * 1024) {
            $this->errors[] = 'Ukuran file melebihi batas maksimal ' . $this->max_size . ' KB.';

            return false;
        }

        $original_name = basename($file['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if ($this->allowed_types !== '') {
            $allowed = array_filter(array_map('trim', explode('|', strtolower($this->allowed_types))));
            if (! in_array($ext, $allowed, true)) {
                $this->errors[] = 'Tipe file tidak diizinkan (ekstensi: ' . $ext . ').';

                return false;
            }
        }

        $disk_name = ($this->file_name !== '' && $this->file_name !== null)
            ? $this->file_name
            : md5(uniqid((string) mt_rand(), true)) . '.' . $ext;

        $upload_dir = rtrim($this->upload_path, '/') . '/';
        if (! is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $full_path = $upload_dir . $disk_name;

        if (! move_uploaded_file($file['tmp_name'], $full_path)) {
            $this->errors[] = 'Gagal memindahkan file ke direktori tujuan.';

            return false;
        }

        $file_type = $file['type'] ?? '';
        $real_mime = function_exists('finfo_open')
            ? (finfo_file(finfo_open(FILEINFO_MIME_TYPE), $full_path) ?: $file_type)
            : $file_type;

        $is_image = (strpos($real_mime, 'image') !== false);
        $width = null;
        $height = null;
        $image_type = '';
        $size_str = '';
        if ($is_image) {
            $info = @getimagesize($full_path);
            if ($info !== false) {
                $width = $info[0];
                $height = $info[1];
                $image_type = $info['mime'];
                $size_str = $width . ' x ' . $height;
                if (($this->max_width > 0 && $width > $this->max_width)
                    || ($this->max_height > 0 && $height > $this->max_height)) {
                    @unlink($full_path);
                    $this->errors[] = 'Dimensi gambar melebihi batas yang diizinkan.';

                    return false;
                }
            }
        }

        $raw_name = pathinfo($disk_name, PATHINFO_FILENAME);
        $this->data = [
            'file_name'     => $disk_name,
            'file_type'     => $real_mime,
            'file_path'     => $upload_dir,
            'full_path'     => $full_path,
            'raw_name'      => $raw_name,
            'orig_name'     => $original_name,
            'client_name'   => $original_name,
            'file_ext'      => '.' . $ext,
            'file_size'     => round($file['size'] / 1024, 2),
            'is_image'      => $is_image,
            'image_width'   => $width,
            'image_height'  => $height,
            'image_type'    => $image_type,
            'image_size_str' => $size_str,
        ];

        return true;
    }

    public function data($index = null)
    {
        if ($index === null) {
            return $this->data;
        }

        return $this->data[$index] ?? null;
    }

    public function display_errors($open = '', $close = '')
    {
        $out = '';
        foreach ($this->errors as $error) {
            $out .= $open . $error . $close;
        }

        return $out;
    }

    public function is_image($index = null)
    {
        return (bool) ($this->data['is_image'] ?? false);
    }
}
