<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

/**
 * The base class for lastModified.
 */
class lastModified
{
    /* @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'lastmodified';
    /** @var array $config */
    public $config = array();

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('lastmodified_core_path', $config,
            $this->modx->getOption('core_path') . 'components/lastmodified/');

        $this->config = array_merge(array(
            'corePath'  => $corePath,
            'modelPath' => $corePath . 'model/',
        ), $config);

        $this->modx->addPackage('lastmodified', $this->config['modelPath']);
        $this->modx->lexicon->load('lastmodified:default');
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     * @param bool  $skipEmpty
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @return bool|int
     */
    public function getIfModifiedSince()
    {
        $ifModifiedSince = false;
        if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
        }
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ifModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
        }

        return $ifModifiedSince;
    }

    /**
     * @return string
     */
    public function getQueryHash()
    {
        $keys = parse_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        if (isset($keys['query'])) {
            parse_str($keys['query'], $query);
            ksort($query);
            $keys['query'] = $query;
        }

        return sha1(serialize($keys));
    }

    /**
     * @param modResource $resource
     *
     * @return string
     */
    public function getOutputHash(modResource $resource)
    {
        $html = $resource->_output;

        if ($resource->get('contentType') == 'text/html') {
            if (preg_match("/<body[^>]*>(.*)[^<]+<\/body>/is", $html, $matches)) {
                $html = $matches[1];
            }
        }

        $skipTags = $this->getOption('skip_tags', null, 'pre,code,script', true);
        $skipTags = $this->explodeAndClean($skipTags);
        foreach ($skipTags as $skipTag) {
            $skipTag = preg_quote($skipTag);
            $pattern = "#<{$skipTag}(.*){$skipTag}>#Usi";
            $html = preg_replace($pattern, '', $html);
        }

        $html = strip_tags($html);
        $html = preg_replace("#[\r\n\t\s]#is", '', $html);

        return sha1($html);
    }

    /**
     * @param modResource $resource
     *
     * @return bool|mixed|null
     */
    public function isWorkingResource(modResource $resource)
    {
        $isWork = $this->modx->getOption('set_header');
        if ($isWork) {
            $isWork = $this->getOption('active', null);
        }

        if ($isWork) {
            $isWork = empty($this->modx->resource->_isForward);
        }

        if ($isWork) {
            $isWork = $resource->get('published');
        }

        if ($isWork AND $this->getOption('check_type', null)) {
            $isWork = in_array($resource->get('contentType'),
                $this->explodeAndClean($this->getOption('working_type')));
        }

        if ($isWork AND $this->getOption('check_template', null)) {
            $isWork = in_array($resource->get('template'),
                $this->explodeAndClean($this->getOption('working_templates')));
        }

        return $isWork;
    }

    /**
     * @param modResource $resource
     *
     * @return mixed|string
     */
    public function getResourceOutput(modResource $resource)
    {
        $html = '';

        $url = $this->modx->makeUrl($resource->id, '', '', 'full');
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $html = curl_exec($curl);
            curl_close($curl);
        } else {
            file_get_contents($url);
        }

        return $html;
    }
}