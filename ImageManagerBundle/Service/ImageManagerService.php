<?php

namespace Daveudaimon\ImageManagerBundle\Service;

use Daveudaimon\ImageManagerBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

define('IMAGE_MANAGER_DIR_HASH', 512);

class ImageManagerService
{
  protected $entityManager;
  protected $rootDir;
  protected $rootUrl;


  public function __construct($doctrine, $rootDir, $rootUrl)
  {
    if (!\file_exists($rootDir))
    {
      throw new Exception(sprintf('Invalid root image dir: "%s"', $rootDir));
    }

    $this->entityManager = $doctrine->getManager();
    $this->rootDir = $rootDir;
    $this->rootUrl = $rootUrl;
  }

  public function setEntityManager($doctrine)
  {
    $this->entityManager = $doctrine->getManager();
  }

  public function getEntityManager()
  {
    return $this->entityManager;
  }

  public function getImagePath($category, $reference)
  {
    $image = $this->getImage($category, $reference);

    return $this->getPath($image);
  }

  public function getImageUrl($category, $reference)
  {
    $image = $this->getImage($category, $reference);

    return $this->getUrl($image);
  }

  public function getImageUrlWithDefault($category, $reference, $default)
  {
    try
    {
      return $this->getImageUrl($category, $reference);
    }
    catch (\Exception $e)
    {
      return $default;
    }
  }

  public function getImageDimensions($category, $reference)
  {
    $imageSize = \getimagesize($this->getImagePath($category, $reference));

    return array(
      'width' => $imageSize[0],
      'height' => $imageSize[1]
    );
  }

  public function getImage($category, $reference, $throwException=true)
  {
    $image = $this->getEntityManager()->getRepository('DaveudaimonImageManagerBundle:Image')
      ->findOneBy(array('category' => $category, 'reference' => $reference));

    if ($throwException && !$image)
    {
      throw new \Exception(sprintf('unable to find image "%s" in category "%s"', $reference, $category));
    }

    return $image;
  }

  public function addImageRecord($category, $reference, $name)
  {
    $image = $this->getImage($category, $reference, false);
    if (!$image)
    {
      $image = new Image();
      $image->setCategory($category);
      $image->setReference($reference);
      $this->getEntityManager()->persist($image);
    }
    else
    {
      if (file_exists($this->getPath($image)))
      {
        $this->removeImageFiles($image);
      }
    }

    $image->setName($this->slugify($name));
    $this->getEntityManager()->flush();

    $imagePath = $this->getPath($image);
    $this->checkPath($imagePath);

    return $image;
  }

  public function addImage($category, $reference, $name, $filePath, $move=false)
  {
    $image = $this->addImageRecord($category, $reference, $name);

    $imagePath = $this->getPath($image);
    if ($move)
    {
      if (!rename($filePath, $imagePath))
      {
        throw new \Exception(sprintf('Unable to move image from "%s" to "%s"', $filePath, $imagePath));
      }
    }
    else
    {
      if (!copy($filePath, $imagePath))
      {
        throw new \Exception(sprintf('Unable to copy image from "%s" to "%s"', $filePath, $imagePath));
      }
    }
  }

  public function addImageFromUpload($category, $reference, $name, UploadedFile $uploadedFile)
  {
    $image = $this->addImageRecord($category, $reference, $name);

    $imagePath = $this->getPath($image);
    $uploadedFile->move(dirname($imagePath), \basename($imagePath));
  }

  public function addImageFromUrl($category, $reference, $name, $url)
  {
    $httpClient = new \Zend_Http_Client($url);
    $httpResponse = $httpClient->request();

    if ($httpResponse->isError())
    {
      throw new \Exception(sprintf('Unable to download image "%s"', $url));
    }

    $tmpPath = '/tmp/img'.md5($name).'.jpg';
    \file_put_contents($tmpPath, $httpResponse->getRawBody());

    $image = $this->addImageRecord($category, $reference, $name);

    $imagePath = $this->getPath($image);
    if (!rename($tmpPath, $imagePath))
    {
      throw new \Exception(sprintf('Unable to move image from "%s" to "%s"', $tmpPath, $imagePath));
    }
  }

  public function getThumbnailUrlWithDefault($category, $reference, $width, $default)
  {
    try
    {
      return $this->getThumbnailUrl($category, $reference, $width);
    }
    catch (\Exception $e)
    {
      return $default;
    }
  }

  public function getThumbnailUrl($category, $reference, $width)
  {
    $image = $this->getImage($category, $reference);

    $this->checkThumbnail($image, $width);

    return $this->getUrl($image, $width);
  }

  public function getThumbnailPath($category, $reference, $width)
  {
    $image = $this->getImage($category, $reference);

    $this->checkThumbnail($image, $width);

    return $this->getPath($image, $width);
  }

  public function checkThumbnail($image, $width)
  {
    if (!\is_integer($width))
    {
      throw new \Exception(sprintf('invalid width "%s"', $width));
    }

    if (!file_exists($this->getPath($image, $width)))
    {
      $this->generateThumbnail($image, $width);
    }
  }

  public function generateThumbnail($image, $width)
  {
    $sourcePath = $this->getPath($image);
    $destPath = $this->getPath($image, $width);

    $this->checkPath($destPath);

    // do not scale up
    list($sourceWidth, $sourceHeight) = \getimagesize($sourcePath);
    if ($sourceWidth < $width)
    {
      $width = $sourceWidth;
    }

    // compute new height
    $height = $sourceHeight * $width / $sourceWidth;

    // Load
    $thumb = imagecreatetruecolor($width, $height);
    $source = imagecreatefromjpeg($sourcePath);

    // Resize
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

    // Output
    imagejpeg($thumb, $destPath);
  }

  public function removeImage($category, $reference)
  {
    $image = $this->getImage($category, $reference);

    $this->removeImageFiles($image);

    $this->getEntityManager()->remove($image);
    $this->getEntityManager()->flush();
  }

  protected function removeImageFiles($image)
  {
    \array_map('\unlink', glob($this->getPathPattern($image)));
  }

  protected function getPath($image, $suffix=null)
  {
    return $this->rootDir.'/'.$this->getLocation($image, $suffix);
  }

  protected function getUrl($image, $suffix=null)
  {
    return $this->rootUrl.'/'.$this->getLocation($image, $suffix);
  }

  protected function getLocation($image, $suffix=null)
  {
    $dir = ceil($image->getId() / IMAGE_MANAGER_DIR_HASH);

    return $image->getCategory().'/'.$dir.'/'.$image->getName().'_'.$image->getId().($suffix ? '_'.$suffix : '').'.jpg';
  }

  protected function getPathPattern($image)
  {
    $dir = ceil($image->getId() / IMAGE_MANAGER_DIR_HASH);

    return $this->rootDir.'/'.$image->getCategory().'/'.$dir.'/'.$image->getName().'_'.$image->getId().'*.jpg';
  }

  protected function checkPath($imagePath)
  {
    $dir = dirname($imagePath);
    if (!\file_exists($dir))
    {
      mkdir($dir, 0777, true);
    }
  }

  /**
   * Modifies a string to remove all non ASCII characters and spaces.
   * @param  string $text The text to slugify
   * @return string       The slugified text
   */
  protected function slugify($string)
  {
    $slug = $string;

   // transliterate
    $slug = iconv("UTF-8", "ASCII//TRANSLIT", $slug);

    // replace non letter or digits by -
    $slug = preg_replace('~[^\\pL\d]+~u', '-', $slug);

    // trim
    $slug = trim($slug, '-');

    // lowercase
    $slug = strtolower($slug);

    // remove unwanted characters
    $slug = preg_replace('~[^-\w]+~', '', $slug);

    // no empty slug!
    if (empty($slug))
    {
      throw new \Exception(sprintf('Empty slugified string for "%s"', $string));
    }

    return $slug;
  }
}
