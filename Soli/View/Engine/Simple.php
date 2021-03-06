<?php
/**
 * @author ueaner <ueaner@gmail.com>
 */
namespace Soli\View\Engine;

use Soli\View\Engine;
use Soli\View\EngineInterface;
use Soli\ViewInterface;
use Soli\Exception;

/**
 * Simple Engine
 *
 * @property null $engine
 */
class Simple extends Engine implements EngineInterface
{
    /**
     * Render
     *
     * @param string $path
     * @param array $vars
     * @return string
     * @throws Exception
     */
    public function render($path, array $vars = null)
    {
        $template = $this->view->getViewsDir() . $path . $this->view->getViewExtension();
        if (!is_readable($template)) {
            throw new Exception("Template file not found: $template.");
        }

        // 设置视图变量
        if (!empty($vars)) {
            extract($vars);
        }

        // 渲染视图
        ob_start();
        require $template;
        return ob_get_clean();
    }
}
