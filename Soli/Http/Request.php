<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\Http;

use Soli\Di\InjectionAwareInterface;
use Soli\Di\Container as DiContainer;

/**
 * 请求
 */
class Request implements InjectionAwareInterface
{
    protected $filter;

    /**
     * @var Soli\Di\Container
     */
    protected $di;

    public function setDi(DiContainer $di)
    {
        $this->di = $di;
    }

    /**
     * @return \Soli\Di\Container
     */
    public function getDi()
    {
        return $this->di;
    }

    /* 请求参数 */

    /**
     * 获取 REQUEST 的某个参数
     *
     * @param string $name
     * @param string $filter
     * @param mixed $defaultValue
     * @return array|string
     */
    public function get($name = null, $filter = null, $defaultValue = null)
    {
        return $this->getHelper($_REQUEST, $name, $filter, $defaultValue);
    }

    /**
     * 获取 GET 的某个参数
     *
     * @param string $name
     * @param string $filter
     * @param mixed $defaultValue
     * @return array|string
     */
    public function getQuery($name = null, $filter = null, $defaultValue = null)
    {
        return $this->getHelper($_GET, $name, $filter, $defaultValue);
    }

    /**
     * 获取 POST 的某个参数
     *
     * @param string $name
     * @param string $filter
     * @param mixed $defaultValue
     * @return array|string
     */
    public function getPost($name = null, $filter = null, $defaultValue = null)
    {
        return $this->getHelper($_POST, $name, $filter, $defaultValue);
    }

    /**
     * 是否有某个参数
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $name && isset($_REQUEST[$name]);
    }

    /* 请求方法 */

    final public function getMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    }

    public function is($method)
    {
        $methods = func_get_args();
        foreach ($methods as $method) {
            $method = strtoupper($method);
            switch ($method) {
                case 'AJAX':
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                        && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
                    ) {
                        return true;
                    }
                    break;
                case 'SOAP':
                    if (isset($_SERVER['HTTP_SOAPACTION'])) {
                        return true;
                    }
                    break;
                default:
                    if ($this->getMethod() == $method) {
                        return true;
                    }
                    break;
            }
        }
        return false;
    }

    /* $_SERVER */

    public function getServer($name = null)
    {
        if (empty($name)) {
            return $_SERVER;
        }
        return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
    }

    public function getClientAddress()
    {
        $address = null;

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $address = $_SERVER['REMOTE_ADDR'];
        }

        return $address;
    }

    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    /* $_COOKIE */

    public function getCookies($name = null, $defaultValue = null)
    {
        if (empty($name)) {
            return $_COOKIE;
        }
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $defaultValue;
    }

    public function removeCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            setcookie($name, '', 1);
        }
    }

    /* protected functions */

    /**
     * 获取过滤变量下标的帮助函数
     *
     * @param array $source
     * @param string $name 变量下标
     * @param string $filter Filter 中的过滤标识
     * @param mixed $defaultValue 默认值
     */
    protected function getHelper(array $source, $name = null, $filter = null, $defaultValue = null)
    {
        if (empty($name)) {
            return $source;
        }

        if (!isset($source[$name])) {
            return $defaultValue;
        }

        $value = $source[$name];
        if ($filter !== null) {
            if (!is_object($this->filter)) {
                $this->filter = $this->di->getShared('filter');
            }
            $value = $this->filter->sanitize($value, $filter);
        }

        return $value;
    }
}
