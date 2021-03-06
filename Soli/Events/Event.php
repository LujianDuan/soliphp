<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\Events;

use Soli\Exception;

/**
 * 事件原型
 */
class Event
{
    /**
     * 事件名称，当事件监听器为对象时，事件名称对应事件监听器中的方法名
     *
     * @var string
     */
    protected $name;

    /**
     * 事件来源
     *
     * @var object
     */
    protected $source;

    /**
     * 事件相关数据
     *
     * @var mixed
     */
    protected $data;

    /**
     * Event constructor.
     *
     * @param string $name
     * @param object $source
     * @param mixed $data
     * @throws Exception
     */
    public function __construct($name, $source, $data = null)
    {
        if (!is_string($name) || !is_object($source)) {
            throw new Exception('Invalid parameter type.');
        }

        $this->name = $name;
        $this->source = $source;

        if ($data !== null) {
            $this->data = $data;
        }
    }

    /**
     * 激活事件监听队列
     *
     * @param array $queue
     * @return mixed
     * @throws Exception
     */
    public function fire($queue)
    {
        if (!is_array($queue)) {
            throw new Exception('The queue is not valid');
        }

        // 事件监听队列中最后一个监听器的执行状态
        $status = null;

        foreach ($queue as $listener) {
            if ($listener instanceof Closure) {
                // 调用闭包监听器
                $status = call_user_func_array($listener, [$this, $this->source, $this->data]);
            } elseif (method_exists($listener, $this->name)) {
                // 调用对象监听器
                $status = $listener->{$this->name}($this, $this->source, $this->data);
            }
        }

        return $status;
    }
}
