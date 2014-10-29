<?php

namespace Notepads\Lib;

/**
 * Class Template
 * @package Notepads\Lib
 *
 */
class Template
{

    /**
     * @var
     */
    private $path;
    /**
     * @var string
     */
    private $viewSubpath;
    /**
     * @var
     */
    private $layout;
    /**
     * @var
     */
    private $template;
    /**
     * @var array
     */
    private $vars = array();
    /**
     * @var array
     */
    private $varsLayout = array();

    /**
     * @param $path
     * @param $controllerName
     * @throws
     */
    public function __construct($path, $controllerName)
    {
        $this->path = $path;
        $this->viewSubpath = $this->path . '/'
            . lcfirst(str_replace("Controller", "", $controllerName));
        if (!is_dir($this->path) || !is_dir($this->viewSubpath)) {
            throw new \InvalidArgumentException("Invalid path(s) set!");
        }
    }

    /**
     * @param $property
     * @param $value
     * @param bool $inLayout
     */
    public function assign($property, $value, $inLayout = false)
    {
        if ($inLayout) {
            $this->varsLayout[$property] = $value;
        } else {
            $this->vars[$property] = $value;
        }
    }

    /**
     * @param $data
     * @param bool $inLayout
     */
    public function assignMany($data, $inLayout = false)
    {
        foreach ($data as $property => $value) {
            $this->assign($property, $value, $inLayout);
        }
    }

    /**
     * @param $filename
     */
    public function setLayout($filename)
    {
        $file = $this->path . "/" . $filename;
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("Invalid layout file path: '$file'");
        }
        $this->layout = $filename;
    }

    /**
     * @param $filename
     */
    public function setTemplate($filename)
    {
        $file = $this->viewSubpath . "/" . $filename;
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("Invalid template file path: '$file'");
        }
        $this->template = $filename;
    }

    /**
     * @param bool $showLayout
     * @SuppressWarnings("PMD.UnusedLocalVariable")
     */
    public function render($showLayout = true)
    {
        if ($showLayout && $this->layout) {
            ob_start();
        }

        extract($this->vars);
        include $this->viewSubpath .  '/' . $this->template;

        if ($showLayout && $this->layout) {
            extract($this->varsLayout);
            $content = ob_get_clean();
            include $this->path . '/' . $this->layout;
        }
    }
}
