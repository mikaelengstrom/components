<?php
namespace DigitalUnited\Components;

abstract class Component
{
    use ShortCodeDriver;

    private $params;
    private $content;

    public function __construct($params = [], $content = '')
    {
        $this->content = $content;
        $this->params = $params;
    }

    public function register()
    {
        $this->registerShortCode();
        $this->main();
    }

    public function render()
    {
        return $this->addWrapperDiv($this->renderTemplate());
    }

    private function addWrapperDiv($innerMarkup)
    {
        $classes = implode(' ', $this->getWrapperDivClasses());
        return "<div class='{$classes}'>$innerMarkup</div>";
    }

    /**
     * @return array Array with which the wrapper div should have
     */
    private function getWrapperDivClasses()
    {
        $className = get_called_class();
        $className = str_replace('\\', '-', $className);
        $className = strtolower($className);

        $classes = [$className, 'du-component'];

        $view = $this->param('view') ? $this->param('view') : $this->param('theme');
        if ($view) {
            $classes[] = str_replace('.', '-', $view);
        }

        return array_merge($classes, $this->getExtraWrapperDivClasses());
    }

    /**
     * @return array Array with extra classes the wrapper div should have
     */
    protected function getExtraWrapperDivClasses()
    {
        return [];
    }

    private function renderTemplate()
    {
        return TemplateEngine::render(
            $this->getViewPath(),
            $this->getSanetizedParams()
        );
    }

    public function renderPartial($viewName)
    {
        return TemplateEngine::render(
            $this->getComponentPath().'/'.$viewName.'.view.php',
            $this->getSanetizedParams()
        );
    }

    private function getComponentPath()
    {
        $reflector = new \ReflectionClass(get_class($this));
        return dirname($reflector->getFileName());
    }

    private function getViewPath()
    {
        $componentPath = $this->getComponentPath();

        $viewFilePaths = [];
        $viewFilePaths[] = $componentPath.'/'.$this->getViewFileName();

        if ($this->param('view')) {
            $viewFilePaths[] = $componentPath.'/'.$this->param('view').'.view.php';
        }

        if ($this->param('theme')) {
            $viewFilePaths[] = $componentPath.'/'.$this->param('theme').'.view.php';
        }
        $viewFilePaths[] = $componentPath.'/'.'view.php';

        foreach ($viewFilePaths as $viewFilePath) {
            if (file_exists($viewFilePath) && is_file($viewFilePath)) {
                return $viewFilePath;
            }
        }

        throw new \Exception(
            'View file is missing in '.$componentPath.'. Tried the following paths: '.implode(', ', $viewFilePaths)
        );
    }

    protected function getViewFileName()
    {
        return '';
    }

    /**
     * return param value if existing
     *
     * @param $paramName String     Parameter index
     *
     * @return Mixed Param value
     */
    protected function param($paramName)
    {
        $fallbacks = $this->getDefaultParams();

        if ($paramName == 'content') {
            return $this->content
                ? $this->content
                : $fallbacks['content'];
        }

        if (isset($this->params[$paramName])) {
            return $this->params[$paramName];
        } elseif (isset($fallbacks[$paramName])) {
            return $fallbacks[$paramName];
        } else {
            return null;
        }
    }

    protected function getSanetizedParams()
    {
        $params = shortcode_atts(
            $this->getDefaultParams(),
            $this->params,
            get_called_class()
        );

        // Append content to param since it will be extracted
        // in the rendering engine.
        $params['content'] = $this->content ? $this->content : '';

        $params['component'] = &$this;

        // Apply local component overrides to params
        return $this->sanetizeDataForRendering($params);
    }

    /**
     * @return array   Key value pair with acceptet params/default
     *                 values
     */
    abstract protected function getDefaultParams();

    /**
     * Components can override this class to modify parameters
     * before they are sent to rendering engine.
     *
     * @param array $params The parameters sent to rendering engine
     *
     * @return array        The modified parameters wich will be
     *                      forwarded to renderng engine
     */
    protected function sanetizeDataForRendering($params)
    {
        return $params;
    }

    /**
     * Runs on ->register. Used to implement logic in top class
     * @return void
     */
    public function main()
    {
    }

    public function __toString()
    {
        return $this->render();
    }
}
