<?php

namespace Cws\Bundle\SVGGraphBundle;

use SVG\Nodes\Structures\SVGDocumentFragment;

/**
 * Interface ISVGGraph
 * @package Cws\Bundle\SVGGraphBundle
 */
interface ISVGGraph
{
    public function create(SVGDocumentFragment $svgDocument);
}
