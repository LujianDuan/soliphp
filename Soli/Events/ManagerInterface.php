<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\Events;

/**
 * 事件管理器接口
 */
interface ManagerInterface
{
    /**
     * 注册某个事件的监听器
     *
     * @param string $name 事件名称
     * @param object|callable $listener 监听器
     */
    public function on($name, $listener);

    /**
     * 移除某个/所有事件的监听器
     *
     * @param string $name
     */
    public function off($name);

    /**
     * 激活某个事件的监听器
     *
     * @param string $name
     * @param object $source
     * @param mixed  $data
     * @return mixed
     */
    public function fire($name, $source, $data = null);
}
