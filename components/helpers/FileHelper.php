<?php

namespace lb\components\helpers;

use GuzzleHttp\Client;
use lb\BaseClass;
use lb\components\consts\IO;
use lb\components\request\RequestContract;
use lb\components\response\ResponseContract;
use lb\Lb;
use RequestKit;
use ResponseKit;

class FileHelper extends BaseClass implements IO
{
    /**
     * Delete a file
     *
     * @param  $file_path
     * @return bool
     */
    public static function delete($file_path)
    {
        $result = false;
        if (self::fileExists($file_path)) {
            $result = unlink($file_path);
        }
        return $result;
    }

    /**
     * Get file extension
     *
     * @param  $file_path
     * @return string
     */
    public static function getExtensionName($file_path)
    {
        $file_extension_name = '';
        if (file_exists($file_path)) {
            $file_extension_name = pathinfo($file_path)['extension'];
        }
        return $file_extension_name;
    }

    /**
     * Get file size
     *
     * @param  $file_path
     * @return int|string
     */
    public static function getSize($file_path)
    {
        $file_size = '';
        if (file_exists($file_path)) {
            $file_size = filesize($file_path);
        }
        return $file_size;
    }

    /**
     * Download a file
     *
     * @param $file_path
     * @param $file_name
     * @param $response
     */
    public static function download($file_path, $file_name, ResponseContract $response = null)
    {
        if (file_exists(iconv('UTF-8', 'GB2312', $file_path))) {
            $file_size = filesize($file_path);
            $fp = fopen($file_path, self::READ_BINARY);
            self::header('Content-type', 'application/octet-stream', $response);
            self::header('Accept-Ranges', 'bytes', $response);
            self::header('Accept-Length', $file_size, $response);
            self::header('Content-Disposition', 'attachment; filename=' . $file_name, $response);
            self::header('Expires', '-1', $response);
            self::header('Cache-Control', 'no_cache', $response);
            self::header('Pragma', 'no-cache', $response);
            //兼容IE11
            $ua = Lb::app()->getUserAgent();
            $encoded_filename = urlencode($file_name);
            if(preg_match("/MSIE/is", $ua) || preg_match(preg_quote("/Trident/7.0/is"), $ua)) {
                self::header('Content-Disposition', 'attachment; filename="' . $encoded_filename . '"', $response);
            } else if (preg_match("/Firefox/", $ua)) {
                self::header('Content-Disposition', 'attachment; filename*="utf8\'\'' . $file_name . '"', $response);
            } else {
                self::header('Content-Disposition', 'attachment; filename="' . $file_name . '"', $response);
            }
            echo fread($fp, $file_size);
            fclose($fp);
            exit;
        }
    }

    /**
     * Set header
     *
     * @param $headerKey
     * @param $headerValue
     * @param ResponseContract|null $response
     */
    protected static function header($headerKey, $headerValue, ResponseContract $response = null)
    {
        if ($response) {
            $response->setHeader($headerKey, $headerValue);
        } else {
            ResponseKit::setHeader($headerKey, $headerValue);
        }
    }

    /**
     * Upload a file
     *
     * @param  $file_name
     * @param  $saved_file_path
     * @param  null            $uploaded_file_type_limit
     * @param  null            $uploaded_file_size_limit
     * @param  null            $uploaded_file_ext_limit
     * @return array
     */
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

    /**
     * Send a file
     *
     * @param  $filePath
     * @param  $remoteFileSystem
     * @return bool
     */
    public static function send($filePath, $remoteFileSystem)
    {
        return (new Client())->put($remoteFileSystem, ['body' => fopen($filePath, self::READ_BINARY)])
            ->getStatusCode() == HttpHelper::STATUS_OK;
    }

    /**
     * Receive a file
     *
     * @param $savePath
     * @param RequestContract|null $request
     */
    public static function receive($savePath, RequestContract $request = null)
    {
        file_put_contents($savePath, $request ? $request->getRawContent() : RequestKit::getRawContent());
    }

    /**
     * Copy a file or a directory
     *
     * @param $src
     * @param $dst
     * @param null $context
     * @return bool
     */
    public static function copy($src, $dst, $context = null)
    {
        return self::resourceExists($src) ? copy($src, $dst, $context) : false;
    }

    /**
     * Move a file or a directory
     *
     * @param $oldName
     * @param $newName
     * @param null $context
     * @return bool
     */
    public static function move($oldName, $newName, $context = null)
    {
        return self::rename($oldName, $newName, $context);
    }

    /**
     * Rename a file or a directory
     *
     * @param $oldName
     * @param $newName
     * @param null $context
     * @return bool
     */
    public static function rename($oldName, $newName, $context = null)
    {
        return self::resourceExists($oldName) ? rename($oldName, $newName, $context) : false;
    }

    /**
     * File exists
     *
     * @param $fileName
     * @return bool
     */
    public static function fileExists($fileName)
    {
        return file_exists($fileName);
    }

    /**
     * Directory exists
     *
     * @param $directory
     * @return bool
     */
    public static function dirExists($directory)
    {
        return is_dir($directory);
    }

    /**
     * Resource exists
     *
     * @param $resource
     * @return bool
     */
    public static function resourceExists($resource)
    {
        return self::fileExists($resource) || self::dirExists($resource);
    }
}
