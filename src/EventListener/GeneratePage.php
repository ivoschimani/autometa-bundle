<?php

namespace Ivo\AutoMetaBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\System;

#[AsHook('generatePage')]
class GeneratePage
{

    function __invoke(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular)
    {
        $responseContext = System::getContainer()->get('contao.routing.response_context_accessor')->getResponseContext();
        if ($responseContext?->has(HtmlHeadBag::class)) {
            $htmlHeadBag = $responseContext->get(HtmlHeadBag::class);
            if ("" == $objPage->description) {
                $htmlHeadBag->setMetaDescription("[[seitenbeschreibung]]");
            }
            if ("" == $objPage->pageTitle) {
                $htmlHeadBag->setTitle("[[seitentitel]]");
            }
            if ("" == $GLOBALS['TL_KEYWORDS']) {
                $GLOBALS['TL_KEYWORDS'] = "[[keywords]]";
            }
        }
    }
}