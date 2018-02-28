<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImageCorner.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Corner operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_corner",
 *   toolkit = "gd",
 *   operation = "iek_image_corner",
 *   label = @Translation("IEK - Corner"),
 *   description = @Translation("Add rounded corner to an image.")
 * )
 */
class ImageCorner extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'radius' => [
        'description' => 'Radius',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Fail when radius is negative.
    if ((int) $arguments['radius'] < 0) {
      throw new \InvalidArgumentException("Invalid radius ('{$arguments['radius']}') specified for the image 'iek_image_corner' operation");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    $data = $arguments;

    $width = $this->getToolkit()->getWidth();
    $height = $this->getToolkit()->getHeight();

    $radius = $data['radius'];

    // Finds unique color.
    do {
      $r = rand(0, 255);
      $g = rand(0, 255);
      $b = rand(0, 255);
    } while (imagecolorexact($this->getToolkit()
      ->getResource(), $r, $g, $b) < 0);

    $new_width = $width;
    $new_height = $height;

    $img = imagecreatetruecolor($new_width, $new_height);
    $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
    imagealphablending($img, FALSE);
    imagesavealpha($img, TRUE);
    imagefilledrectangle($img, 0, 0, $new_width, $new_height, $alphacolor);

    imagefill($img, 0, 0, $alphacolor);
    imagecopyresampled($img, $this->getToolkit()
      ->getResource(), 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
    imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
    imagearc($img, $new_width - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
    imagefilltoborder($img, $new_width - 1, 0, $alphacolor, $alphacolor);
    imagearc($img, $radius - 1, $new_height - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
    imagefilltoborder($img, 0, $new_height - 1, $alphacolor, $alphacolor);
    imagearc($img, $new_width - $radius, $new_height - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
    imagefilltoborder($img, $new_width - 1, $new_height - 1, $alphacolor, $alphacolor);
    imagealphablending($img, TRUE);
    imagecolortransparent($img, $alphacolor);

    // Resizes image down.
    $dst = imagecreatetruecolor($width, $height);
    imagealphablending($dst, FALSE);
    imagesavealpha($dst, TRUE);
    imagefilledrectangle($dst, 0, 0, $width, $height, $alphacolor);
    imagecopyresampled($dst, $img, 0, 0, 0, 0, $width, $height, $new_width, $new_height);

    imagedestroy($this->getToolkit()->getResource());

    // Update image object.
    $this->getToolkit()->setResource($dst);

    return TRUE;
  }

}
