<?php

namespace lb\components\helpers;

use lb\BaseClass;
use lb\components\response\ResponseContract;
use ResponseKit;

class HttpHelper extends  BaseClass
{
    const STATUS_OK = 200;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_GATEWAY_TIMEOUT = 504;

    const MIME_TYPES = array (
        'apk'     => 'application/vnd.android.package-archive',
        '3gp'     => 'video/3gpp',
        '3gpp'    => 'video/3gpp',
        'ai'      => 'application/postscript',
        'aif'     => 'audio/x-aiff',
        'aifc'    => 'audio/x-aiff',
        'aiff'    => 'audio/x-aiff',
        'asc'     => 'text/plain',
        'atom'    => 'application/atom+xml',
        'au'      => 'audio/basic',
        'avi'     => 'video/x-msvideo',
        'bcpio'   => 'application/x-bcpio',
        'bin'     => 'application/octet-stream',
        'bmp'     => 'image/bmp',
        'cab'     => 'application/octet-stream',
        'cdf'     => 'application/x-netcdf',
        'cgm'     => 'image/cgm',
        'chm'     => 'application/octet-stream',
        'class'   => 'application/octet-stream',
        'cpio'    => 'application/x-cpio',
        'cpt'     => 'application/mac-compactpro',
        'csh'     => 'application/x-csh',
        'css'     => 'text/css',
        'csv'     => 'text/comma-separated-values',
        'dcr'     => 'application/x-director',
        'dif'     => 'video/x-dv',
        'dir'     => 'application/x-director',
        'djv'     => 'image/vnd.djvu',
        'djvu'    => 'image/vnd.djvu',
        'dll'     => 'application/octet-stream',
        'dmg'     => 'application/octet-stream',
        'dms'     => 'application/octet-stream',
        'doc'     => 'application/msword',
        'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dtd'     => 'application/xml-dtd',
        'dv'      => 'video/x-dv',
        'dvi'     => 'application/x-dvi',
        'dxr'     => 'application/x-director',
        'eps'     => 'application/postscript',
        'etx'     => 'text/x-setext',
        'exe'     => 'application/octet-stream',
        'ez'      => 'application/andrew-inset',
        'flv'     => 'video/x-flv',
        'gif'     => 'image/gif',
        'gram'    => 'application/srgs',
        'grxml'   => 'application/srgs+xml',
        'gtar'    => 'application/x-gtar',
        'gz'      => 'application/x-gzip',
        'hdf'     => 'application/x-hdf',
        'hqx'     => 'application/mac-binhex40',
        'htm'     => 'text/html',
        'html'    => 'text/html',
        'ice'     => 'x-conference/x-cooltalk',
        'ico'     => 'image/x-icon',
        'ics'     => 'text/calendar',
        'ief'     => 'image/ief',
        'ifb'     => 'text/calendar',
        'iges'    => 'model/iges',
        'igs'     => 'model/iges',
        'jnlp'    => 'application/x-java-jnlp-file',
        'jp2'     => 'image/jp2',
        'jpe'     => 'image/jpeg',
        'jpeg'    => 'image/jpeg',
        'jpg'     => 'image/jpeg',
        'js'      => 'application/x-javascript',
        'kar'     => 'audio/midi',
        'latex'   => 'application/x-latex',
        'lha'     => 'application/octet-stream',
        'lzh'     => 'application/octet-stream',
        'm3u'     => 'audio/x-mpegurl',
        'm4a'     => 'audio/mp4a-latm',
        'm4p'     => 'audio/mp4a-latm',
        'm4u'     => 'video/vnd.mpegurl',
        'm4v'     => 'video/x-m4v',
        'mac'     => 'image/x-macpaint',
        'man'     => 'application/x-troff-man',
        'mathml'  => 'application/mathml+xml',
        'me'      => 'application/x-troff-me',
        'mesh'    => 'model/mesh',
        'mht'     => 'message/rfc822',
        'mhtml'   => 'message/rfc822',
        'mid'     => 'audio/midi',
        'midi'    => 'audio/midi',
        'mif'     => 'application/vnd.mif',
        'mov'     => 'video/quicktime',
        'movie'   => 'video/x-sgi-movie',
        'mp2'     => 'audio/mpeg',
        'mp3'     => 'audio/mpeg',
        'mp4'     => 'video/mp4',
        'mpe'     => 'video/mpeg',
        'mpeg'    => 'video/mpeg',
        'mpg'     => 'video/mpeg',
        'mpga'    => 'audio/mpeg',
        'ms'      => 'application/x-troff-ms',
        'msh'     => 'model/mesh',
        'msi'     => 'application/octet-stream',
        'mxu'     => 'video/vnd.mpegurl',
        'nc'      => 'application/x-netcdf',
        'ocx'     => 'application/octet-stream',
        'oda'     => 'application/oda',
        'ogg'     => 'application/ogg',
        'ogv'     => 'video/ogv',
        'pbm'     => 'image/x-portable-bitmap',
        'pct'     => 'image/pict',
        'pdb'     => 'chemical/x-pdb',
        'pdf'     => 'application/pdf',
        'pgm'     => 'image/x-portable-graymap',
        'pgn'     => 'application/x-chess-pgn',
        'pic'     => 'image/pict',
        'pict'    => 'image/pict',
        'png'     => 'image/png',
        'pnm'     => 'image/x-portable-anymap',
        'pnt'     => 'image/x-macpaint',
        'pntg'    => 'image/x-macpaint',
        'ppm'     => 'image/x-portable-pixmap',
        'ppt'     => 'application/vnd.ms-powerpoint',
        'pptx'    => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pps'     => 'application/vnd.ms-powerpoint',
        'ppsx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ps'      => 'application/postscript',
        'qt'      => 'video/quicktime',
        'qti'     => 'image/x-quicktime',
        'qtif'    => 'image/x-quicktime',
        'ra'      => 'audio/x-pn-realaudio',
        'ram'     => 'audio/x-pn-realaudio',
        'rar'     => 'application/octet-stream',
        'ras'     => 'image/x-cmu-raster',
        'rdf'     => 'application/rdf+xml',
        'rgb'     => 'image/x-rgb',
        'rm'      => 'application/vnd.rn-realmedia',
        'rmi'     => 'audio/mid',
        'roff'    => 'application/x-troff',
        'rtf'     => 'text/rtf',
        'rtx'     => 'text/richtext',
        'sgm'     => 'text/sgml',
        'sgml'    => 'text/sgml',
        'sh'      => 'application/x-sh',
        'shar'    => 'application/x-shar',
        'silo'    => 'model/mesh',
        'sit'     => 'application/x-stuffit',
        'skd'     => 'application/x-koan',
        'skm'     => 'application/x-koan',
        'skp'     => 'application/x-koan',
        'skt'     => 'application/x-koan',
        'smi'     => 'application/smil',
        'smil'    => 'application/smil',
        'snd'     => 'audio/basic',
        'so'      => 'application/octet-stream',
        'spl'     => 'application/x-futuresplash',
        'src'     => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc'  => 'application/x-sv4crc',
        'svg'     => 'image/svg+xml',
        'svgz'    => 'image/svg+xml',
        'swf'     => 'application/x-shockwave-flash',
        't'       => 'application/x-troff',
        'tar'     => 'application/x-tar',
        'tcl'     => 'application/x-tcl',
        'tex'     => 'application/x-tex',
        'texi'    => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tgz'     => 'application/x-compressed',
        'tif'     => 'image/tiff',
        'tiff'    => 'image/tiff',
        'tr'      => 'application/x-troff',
        'tsv'     => 'text/tab-separated-values',
        'txt'     => 'text/plain',
        'ustar'   => 'application/x-ustar',
        'vcd'     => 'application/x-cdlink',
        'vrml'    => 'model/vrml',
        'vxml'    => 'application/voicexml+xml',
        'wav'     => 'audio/x-wav',
        'wbmp'    => 'image/vnd.wap.wbmp',
        'wbxml'   => 'application/vnd.wap.wbxml',
        'webm'    => 'video/webm',
        'webp'    => 'image/webp',
        'wma'     => 'audio/x-ms-wma',
        'wml'     => 'text/vnd.wap.wml',
        'wmlc'    => 'application/vnd.wap.wmlc',
        'wmls'    => 'text/vnd.wap.wmlscript',
        'wmlsc'   => 'application/vnd.wap.wmlscriptc',
        'wmv'     => 'video/x-ms-wmv',
        'wrl'     => 'model/vrml',
        'xbm'     => 'image/x-xbitmap',
        'xht'     => 'application/xhtml+xml',
        'xhtml'   => 'application/xhtml+xml',
        'xls'     => 'application/vnd.ms-excel',
        'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml'     => 'application/xml',
        'xpm'     => 'image/x-xpixmap',
        'xsl'     => 'application/xml',
        'xslt'    => 'application/xslt+xml',
        'xul'     => 'application/vnd.mozilla.xul+xml',
        'xwd'     => 'image/x-xwindowdump',
        'xyz'     => 'chemical/x-xyz',
        'z'       => 'application/x-compress',
        'zip'     => 'application/zip'
    );

    /**
     * 根据文件后缀获取mime类型
     * @param  string $ext 文件后缀
     * @return string      mime类型
     */
    public static function get_mime_type($ext){
        $mime_types = static::MIME_TYPES;
        return isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
    }

    /**
     * 根据mime类型获取文件后缀
     * @param  string $mime_type mime类型
     * @return string      文件后缀
     */
    public static function get_ext($mime_type){
        $mime_types = static::MIME_TYPES;
        $mime_types = array_flip($mime_types);
        return isset($mime_types[$mime_type]) ? $mime_types[$mime_type] : '';
    }

    /**
     * 根据HTTP状态码获取提示信息
     * @param $status_code
     * @return string
     */
    public static function get_status_code_message($status_code)
    {
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            self::STATUS_OK => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            self::STATUS_BAD_REQUEST => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            self::STATUS_NOT_FOUND => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            self::STATUS_GATEWAY_TIMEOUT => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$status_code])) ? $codes[$status_code] : '';
    }

    /**
     * Set http cache
     *
     * @param $cache_control
     * @param $offset
     * @param $response ResponseContract
     */
    public static function setCache($cache_control, $offset, $response = null)
    {
        if ($response) {
            $response->setHeader('Cache-Control', $cache_control);
        } else {
            ResponseKit::setHeader('Cache-Control', $cache_control);
        }
        if ($response) {
            $response->setHeader('Expires', gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
        } else {
            ResponseKit::setHeader('Expires', gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
        }
    }

    /**
     * Parse Query String
     *
     * @param $url
     * @param $callable
     * @return array
     */
    public static function parseQuery($url, $callable = null)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
        if ($callable) {
            call_user_func_array($callable, ['queryParams' => $queryParams]);
        }
        return $queryParams;
    }
}
