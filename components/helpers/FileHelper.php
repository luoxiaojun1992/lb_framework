<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\components\consts\IO;
use lb\Lb;

class FileHelper extends BaseClass implements IO
{
    public static function delete($file_path)
    {
        $result = false;
        if (file_exists($file_path)) {
            $result = unlink($file_path);
        }
        return $result;
    }

    public static function getExtensionName($file_path)
    {
        $file_extension_name = '';
        if (file_exists($file_path)) {
            $file_extension_name = pathinfo($file_path)['extension'];
        }
        return $file_extension_name;
    }

    public static function getSize($file_path)
    {
        $file_size = '';
        if (file_exists($file_path)) {
            $file_size = filesize($file_path);
        }
        return $file_size;
    }

    public static function download($file_path, $file_name)
    {
        if (file_exists(iconv('UTF-8', 'GB2312', $file_path))) {
            $file_size = filesize($file_path);
            $fp = fopen($file_path, self::READ_BINARY);
            Header('Content-type: application/octet-stream');
            Header('Accept-Ranges: bytes');
            Header('Accept-Length: ' . $file_size);
            Header('Content-Disposition: attachment; filename=' . $file_name);
            Header("Expires:-1");
            Header("Cache-Control:no_cache");
            Header("Pragma:no-cache");
            //兼容IE11
            $ua = Lb::app()->getUserAgent();
            $encoded_filename = urlencode($file_name);
            if(preg_match("/MSIE/is", $ua) || preg_match(preg_quote("/Trident/7.0/is"), $ua)){
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
            } else if (preg_match("/Firefox/", $ua)) {
                header('Content-Disposition: attachment; filename*="utf8\'\'' . $file_name . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file_name . '"');
            }
            echo fread($fp, $file_size);
            fclose($fp);
            exit;
        }
    }

    public static function upload($file_name, $saved_file_path, $uploaded_file_type_limit = null, $uploaded_file_size_limit = null, $uploaded_file_ext_limit = null)
    {
        $storage = new \Upload\Storage\FileSystem($saved_file_path);
        $file = new \Upload\File($file_name, $storage);

        // Optionally you can rename the file on upload
        $new_filename = uniqid();
        $file->setName($new_filename);

        // Validate file upload
        // MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
        $validations = [];
        if ($uploaded_file_type_limit) {
            //You can also add multi mimetype validation
            $validations[] = new \Upload\Validation\Mimetype($uploaded_file_type_limit);
        }
        if ($uploaded_file_size_limit) {
            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            $validations[] = new \Upload\Validation\Size($uploaded_file_size_limit);
        }
        if ($uploaded_file_ext_limit) {
            $validations[] = new \Upload\Validation\Extension($uploaded_file_ext_limit);
        }
        if ($validations) {
            $file->addValidations($validations);
        }

        // Access data about the file that has been uploaded
        $data = array(
            'name'       => $file->getNameWithExtension(),
            'extension'  => $file->getExtension(),
            'mime'       => $file->getMimetype(),
            'size'       => $file->getSize(),
            'md5'        => $file->getMd5(),
            'dimensions' => $file->getDimensions()
        );

        // Try to upload file
        try {
            // Success!
            $file->upload();
            return ['result' => 'success', 'new_name' => $new_filename, 'data' => $data];
        } catch (\Exception $e) {
            // Fail!
            $errors = $file->getErrors();
            return ['result' => 'failed', 'errors' => $errors];
        }
    }
}
