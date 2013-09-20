<?php

namespace daveudaimon\XsltBundle\Service;

class XsltService
{
  protected $root;

  public function __construct($root)
  {
    $this->root = $root;
  }

  /**
   * Apply an xsl on an xml string
   * 
   * @param  string $xml the xml string to transform
   * @param  string $xsl the name of the xsl stylesheet
   * @return string the transformed xml string
   */
  public function transform($xml, $xsl)
  {
    $xmlDoc = new \DOMDocument();
    $xmlDoc->loadXml($xml);

    $xslt = new \XSLTProcessor();
    $xslDoc = $this->getStyleSheet($xsl);
    $xslt->importStyleSheet($xslDoc);

    return $xslt->transformToXML($xmlDoc);
  }

  protected function getStyleSheet($xsl)
  {
    $xslDoc = new \DOMDocument();
    $xslDoc->load($this->root.'/'.$xsl.'.xsl');

    return $xslDoc;
  }
}