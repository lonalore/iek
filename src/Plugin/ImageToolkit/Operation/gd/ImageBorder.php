<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImageBorder.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Border operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_border",
 *   toolkit = "gd",
 *   operation = "iek_image_border",
 *   label = @Translation("IEK - Border"),
 *   description = @Translation("Add border to an image.")
 * )
 */
class ImageBorder extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'border_color' => [
        'description' => 'Border color',
      ],
      'border_thick_top' => [
        'description' => 'Border thick - top',
      ],
      'border_thick_right' => [
        'description' => 'Border thick - right',
      ],
      'border_thick_bottom' => [
        'description' => 'Border thick - bottom',
      ],
      'border_thick_left' => [
        'description' => 'Border thick - left',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // TODO - validate color code.

    // Fail when border width is 0 or negative.
    if ((int) $arguments['border_thick_top'] <= 0) {
      throw new \InvalidArgumentException("Invalid border width ('{$arguments['border_thick_top']}') specified for the image 'iek_image_border' operation");
    }

    // Fail when border width is 0 or negative.
    if ((int) $arguments['border_thick_right'] <= 0) {
      throw new \InvalidArgumentException("Invalid border width ('{$arguments['border_thick_right']}') specified for the image 'iek_image_border' operation");
    }

    // Fail when border width is 0 or negative.
    if ((int) $arguments['border_thick_bottom'] <= 0) {
      throw new \InvalidArgumentException("Invalid border width ('{$arguments['border_thick_bottom']}') specified for the image 'iek_image_border' operation");
    }

    // Fail when border width is 0 or negative.
    if ((int) $arguments['border_thick_left'] <= 0) {
      throw new \InvalidArgumentException("Invalid border width ('{$arguments['border_thick_left']}') specified for the image 'iek_image_border' operation");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    $data = $arguments;

    $border_color = $data['border_color'];
    $border_thick_top = $data['border_thick_top'];
    $border_thick_right = $data['border_thick_right'];
    $border_thick_bottom = $data['border_thick_bottom'];
    $border_thick_left = $data['border_thick_left'];

    $width = $this->getToolkit()->getWidth();
    $height = $this->getToolkit()->getHeight();

    $border_rgb = iek_hex2rgb($border_color);
    $bg_rgb = iek_hex2rgb('#ffffff');

    $dst = imagecreatetruecolor($width, $height);
    // Creates background.
    $bg = imagecolorallocate($dst, $bg_rgb['red'], $bg_rgb['green'], $bg_rgb['blue']);
    // Defines border color.
    $border_colors = imagecolorallocate($dst, $border_rgb['red'], $border_rgb['green'], $border_rgb['blue']);

    imagefilledrectangle($dst, 0, 0, $width, $height, $border_colors);
    imagefilledrectangle($dst, $border_thick_left, $border_thick_top, $width - $border_thick_right - 1, $height - $border_thick_bottom - 1, $bg);

    $this->getToolkit()->apply('iek_image_resize', [
      'width' => $width - ($border_thick_left + $border_thick_right),
      'height' => $height - ($border_thick_top + $border_thick_bottom),
      'blank_margin' => FALSE,
      'blank_margin_bg_color' => $border_color,
      'position' => 'middle_center',
      'x' => 0,
      'y' => 0,
    ]);

    if (!imagecopy($dst,
      $this->getToolkit()->getResource(),
      $border_thick_left,
      $border_thick_top,
      0,
      0,
      $width - ($border_thick_left + $border_thick_right),
      $height - ($border_thick_top + $border_thick_bottom))
    ) {
      return FALSE;
    }

    imagedestroy($this->getToolkit()->getResource());

    // Update image object.
    $this->getToolkit()->setResource($dst);

    return TRUE;
  }

}
