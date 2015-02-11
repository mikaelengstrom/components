# Components #

# Installation
1. Install via composer
2. Activate plugin
3. Configure grunt

# Usage
A component folder should be placed in the theme-directory following this structure:

```
└── components
    ├── SomeComponent
    │   ├── assets
    |   |   └── some-jpgs-or-whatever.jpg
    │   ├── component.php
    │   ├── one-or-more-less-files.less
    │   ├── one-or-more-coffee-files.coffee
    │   └── view.php
    └── SomeOtherComponent
        ├── component.php
        ├── one-or-more-less-files.less
        ├── one-or-more-coffee-files.coffee
        └── view.php
```
Every component will have their short-code registered and vc_mapping set up.


## component.php

A VCComponent have the following structure:

```
namespace Component;

class Text extends \DigitalUnited\Components\VcComponent
{
    // This is a VC-mapping array
    // https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332

    protected function getComponentConfig()
    {
        return [
            'name' => __('Text', 'components'),
            'description' => __('Standard textmodul', 'components'),
            'params' => [
                [
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __( "Headline", "components" ),
                    "param_name" => "headline",
                    "value" => "",
                    "description" => ""
                ],
                [
                    "type" => "textarea_html",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __( "Content", "components" ),
                    "param_name" => "content",
                    "value" => "",
                    "description" => ""
                ],
            ]
        ];
    }

    // If you want to you can have diferent views for deferent cases.
    // If you do you can override the following method.
    //
    // default is __DIR__.'/view.php'
    protected function getViewFileName() {
        return parent::getViewFileName();
    }

    // Before the parameters of the components is sent to rendering
    // you may modify their values here
    protected function sanetizeDataForRendering($data)
    {
        return $data;
    }
    
    // Override the classes the wrapping div will obtain.
    // parent::getWrapperDivClasses() returns ['namespace-componentname']
    protected function getWrapperDivClasses()
    {
    }

    // May be used to implement logic such as post-type registering or whatever
    public function main()
    {
    }
}
?>
```

A Standard component have the following structure:


```
namespace Component;

class Sidebar extends \DigitalUnited\Components\Component
{
    // Return key value pair with the accepted parameters for this
    // view file
    protected function getDefaultParamValues() {
        return [
            'param1' => 'default value1',
            'param2' => ''
        ];
    }

    //Same as a VcComponent
    protected function getViewFileName() { ... }
    protected function sanetizeDataForRendering($data) { ... }
    public function main() { ... }
}
?>
```

## View
In the views, all values returned from "sanetizeDataForRendering" will be accessible.

eg. ['foo' => 'bar'] will be available like
```
<?= $foo // outputs 'bar' ?>
```

You may also use the component class, referenced as $this. eg:
```
<?= $this->myFancyPublicFunction() ?>
```

## Less and coffe, assets
Could be handled with with Grunt or whatever.
See https://github.com/digitalunited/roots for example
