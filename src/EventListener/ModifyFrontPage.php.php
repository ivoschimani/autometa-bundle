<?php

namespace Ivo\AutoMetaBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\PageModel;

#[AsHook('modifyFrontendPage')]
class ModifyFrontendPage
{

    function __invoke($buffer, $templateName)
    {
        $title = null;
        $strContent = $buffer;
        if (mb_detect_encoding($strContent) != "UTF-8") {
            $strContent = \mb_convert_encoding($strContent, "UTF-8");
        }
        if ("" == $strContent) {
            return;
        }
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        if (!$dom->loadHTML($strContent)) {
            foreach (libxml_get_errors() as $error) {
            }

            libxml_clear_errors();
        }
        $dom->formatOutput = true;
        $node_list = $dom->getElementsByTagName("h1");
        $text = "";
        foreach ($node_list as $node) {
            $parentNode = $node->parentNode;
            $node_list = $parentNode->getElementsByTagName("p");
            foreach ($node_list as $node) {
                $nodeText = trim(preg_replace('/\s+/', ' ', strip_tags($node->textContent)));
                if (strpos($nodeText, "text/javascript") || strpos($nodeText, "(function")) {
                    continue;
                }
                if (strlen($nodeText) > 130) {
                    $text = $nodeText;
                    break;
                }
            }
            break;
        }
        if (!$text || "" == $text) {
            $node_list = $dom->getElementsByTagName("p");
            foreach ($node_list as $node) {
                $nodeText = trim(preg_replace('/\s+/', ' ', strip_tags($node->textContent)));
                if (strpos($nodeText, "text/javascript") || strpos($nodeText, "(function")) {

                    continue;
                }
                if (strlen($nodeText) > 130) {

                    $text = $nodeText;

                    break;
                }
            }
        }
        $text = str_replace("[nbsp]", " ", $text);
        $node_list = $dom->getElementsByTagName("h1");
        foreach ($node_list as $node) {
            $nodeText = trim(preg_replace('/\s+/', ' ', strip_tags($node->textContent)));
            $title = $nodeText;
            break;
        }
        $description = preg_replace("/[^ ]*$/", '', substr($text, 0, 130)) . "...";
        $description = preg_replace("/[^a-z\d_äöüßÄÖÜ,. ]/si", '', $description);
        $text = preg_replace("/[^a-z\d_äöüßÄÖÜ ]/si", '', $text);
        $text = str_replace('"', '', $text);
        $pageTitle = $title;
        $buffer = str_replace(array("[[seitenbeschreibung]]", "[[seitentitel]]"), array($description, $pageTitle), $buffer);

        return $buffer;
    }
}