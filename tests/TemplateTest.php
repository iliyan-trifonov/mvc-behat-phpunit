<?php

namespace Notepads\Tests;

use Notepads\Lib\Template;
use Notepads\Tests\PHPUnit\BaseTestClass;

class TemplateTest extends BaseTestClass
{
    public function testTemplates()
    {
        $templatevar1 = "insidetemplate1";
        $templatevar2 = "insidetemplate2";
        $templatevar3 = "insidetemplate3";

        $layoutvar1 = "insidelayout1";
        $layoutvar2 = "insidelayout2";
        $layoutvar3 = "insidelayout3";

        $tpl = new Template(__DIR__ . '/test-phtmls/', 'Testctrl');
        $tpl->assign("templatevar1", $templatevar1);
        $tpl->assign("layoutvar1", $layoutvar1, true);
        $tpl->assignMany(array(
            "templatevar2" => $templatevar2,
            "templatevar3" => $templatevar3,
        ));
        $tpl->assignMany(
            array(
                "layoutvar2" => $layoutvar2,
                "layoutvar3" => $layoutvar3,
            ),
            true
        );
        $tpl->setLayout("layout.phtml");
        $tpl->setTemplate("template.phtml");

        ob_start();
        $tpl->render();
        $result = ob_get_clean();

        $this->assertContains($templatevar1, $result);
        $this->assertContains($templatevar2, $result);
        $this->assertContains($templatevar3, $result);
        $this->assertContains($layoutvar1, $result);
        $this->assertContains($layoutvar2, $result);
        $this->assertContains($layoutvar3, $result);
    }

    public function testNoParamsGiven()
    {
        $this->setExpectedException("\\InvalidArgumentException", "Invalid path(s) set!");
        new Template("nopath", "");
    }

    public function testWrongMainPathGiven()
    {
        $this->setExpectedException("\\InvalidArgumentException", "Invalid path(s) set!");
        new Template("nopath", "");
    }

    public function testWrongSubTemplatePathGiven()
    {
        $this->setExpectedException("\\InvalidArgumentException", "Invalid path(s) set!");
        new Template(__DIR__ . "/test-phtmls/", "Unknown");
    }

    public function testWrongLayoutFileGiven()
    {
        $path = __DIR__ . "/test-phtmls";
        $controller = "Testctrl";
        $file =  "nofile.phtml";
        $fullPath = $path . "/" . $file;
            $this->setExpectedException("\\InvalidArgumentException", "Invalid layout file path: '$fullPath'");
        (new Template($path, $controller))->setLayout($file);
    }

    public function testWrongTemplateFileGiven()
    {
        $path = __DIR__ . "/test-phtmls";
        $controller = "Testctrl";
        $file =  "nofile.phtml";
        $fullPath = $path . "/" . lcfirst($controller) . "/" . $file;
            $this->setExpectedException("\\InvalidArgumentException", "Invalid template file path: '$fullPath'");
        (new Template($path, $controller))->setTemplate($file);
    }
}
