<?php

namespace daveudaimon\UtilsBundle\Tools;

class dmTools
{
	/**
	 * Modifies a string to remove all non ASCII characters and spaces.
	 */
	static public function slugify($string)
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
