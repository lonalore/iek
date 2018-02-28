<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImagePadding.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Padding operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_padding",
 *   toolkit = "gd",
 *   operation = "iek_image_padding",
 *   label = @Translation("IEK - Padding"),
 *   description = @Translation("Add padding to an image.")
 * )
 */
class ImagePadding extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'padding_top' => [
        'description' => 'Padding top',
      ],
      'padding_right' => [
        'description' => 'Padding right',
      ],
      'padding_bottom' => [
        'description' => 'Padding bottom',
      ],
      'padding_left' => [
        'description' => 'Padding left',
      ],
      'bg_color' => [
        'description' => 'Background color',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Fail when padding is negative.
    if ((int) $arguments['padding_top'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_top']}') specified for the image 'iek_image_padding' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_right'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_right']}') specified for the image 'iek_image_padding' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_bottom'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_bottom']}') specified for the image 'iek_image_padding' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_left'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_left']}') specified for the image 'iek_image_padding' operation");
    }

    // TODO - validate color code.

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    $data = $arguments;

    $width = $this->getToolkit()->getWidth();
    $height = $this->getToolkit()->getHeight();

    $padding_top = $data['padding_top'];
    $padding_right = $data['padding_right'];
    $padding_bottom = $data['padding_bottom'];
    $padding_left = $data['padding_left'];
    $bg_color = $data['bg_color'];

    $dst = imagecreatetruecolor($width, $height);
    $bg_rgb = iek_hex2rgb($bg_color);
    $bg = imagecolorallocate($dst, $bg_rgb['red'], $bg_rgb['green'], $bg_rgb['blue']);
    imagefilledrectangle($dst, 0, 0, $width, $height, $bg);

    $this->getToolkit()->apply('iek_image_resize', [
      'width' => $width - ($padding_left + $padding_right),
      'height' => $height - ($padding_top + $padding_bottom),
      'blank_margin' => TRUE,
      'blank_margin_bg_color' => $bg_color,
      'position' => 'middle_center',
      'x' => 0,
      'y' => 0,
    ]);

    if (!imagecopy($dst,
      $this->getToolkit()->getResource(),
      $padding_left,
      $padding_top,
      0,
      0,
      ($width - ($padding_left + $padding_right)),
      ($height - ($padding_top + $padding_bottom)))
    ) {
      return FALSE;
    }

    imagedestroy($this->getToolkit()->getResource());

    // Update image object.
    $this->getToolkit()->setResource($dst);

    return TRUE;
  }

}
