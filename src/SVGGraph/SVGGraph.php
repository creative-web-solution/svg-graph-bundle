<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph;

use Cws\Bundle\SVGGraphBundle\SVGGraph\Donut\Donut;
use Cws\Bundle\SVGGraphBundle\SVGGraph\Lines\Lines;
use SVG\SVG;

class SVGGraph
{
    /**
     * Create a donut graph
     *
     * @param $data
     * @param $graphStyle
     * @return string
     */
    public static function createDonut($data, $graphStyle)
    {
        $output = [];

        $graph = new SVG($graphStyle->width, $graphStyle->height);
        $svgDocument = $graph->getDocument();

        $donut = new Donut($data, $graphStyle);
        $donut->create($svgDocument);

        $output[] = "<div class=\"$graphStyle->cssClass\">";
        $output[] = $graph->toXMLString();
        $output[] = $donut->getLegend();
        $output[] = '</div>';

        return implode('', $output);
    }

    /**
     * Create a lines graph
     *
     * @param $data
     * @param $graphStyle
     * @return string
     */
    public static function createLines($data, $graphStyle)
    {
        $output = [];

        $graph = new SVG($graphStyle->width, $graphStyle->height);
        $svgDocument = $graph->getDocument();

        $lines = new Lines($data, $graphStyle);
        $lines->create($svgDocument);

        $output[] = "<div class=\"$graphStyle->cssClass\">";
        $output[] = $graph->toXMLString();
        $output[] = $lines->getLegend();
        $output[] = '</div>';

        return implode('', $output);
    }
}
