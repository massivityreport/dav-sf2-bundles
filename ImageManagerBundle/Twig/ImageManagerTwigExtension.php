<?php

namespace Daveudaimon\ImageManagerBundle\Twig;

use Daveudaimon\ImageManagerBundle\Service\ImageManagerService;

class ImageManagerTwigExtension extends \Twig_Extension
{
  protected $imageManager;

  public function __construct(ImageManagerService $imageManager)
  {
    $this->imageManager = $imageManager;
  }

  public function getName()
  {
    return 'image_manager';
  }

  public function getFunctions()
  {
    return array(
      'get_image_url' => new \Twig_Function_Method($this, 'getImageUrl'),
      'get_thumbnail_url' => new \Twig_Function_Method($this, 'getThumbnailUrl'),
    );
  }

  public function getImageUrl($category, $reference, $default=null)
  {
    if ($default)
    {
      return $this->imageManager->getImageUrlWithDefault($category, $reference);
    }

    return $this->imageManager->getImageUrl($category, $reference);
  }

  public function getThumbnailUrl($category, $reference, $width, $default=null)
  {
    if ($default)
    {
      return $this->imageManager->getThumbnailUrlWithDefault($category, $reference, $width, $default);
    }

    return $this->imageManager->getThumbnailUrl($category, $reference, $width);
  }
}
