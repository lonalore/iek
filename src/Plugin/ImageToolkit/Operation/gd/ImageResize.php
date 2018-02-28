<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImageResize.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Resize operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_resize",
 *   toolkit = "gd",
 *   operation = "iek_image_resize",
 *   label = @Translation("IEK - Resize"),
 *   description = @Translation("Resize an image by using the GD toolkit.")
 * )
 */
class ImageResize extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'width' => [
        'description' => 'Width',
      ],
      'height' => [
        'description' => 'Height',
      ],
      'blank_margin' => [
        'description' => 'Blank margin',
      ],
      'blank_margin_bg_color' => [
        'description' => 'Blank margin background color',
      ],
      'position' => [
        'description' => 'Align position',
      ],
      'x' => [
        'description' => 'X-axis',
      ],
      'y' => [
        'description' => 'Y-axis',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Fail when width is 0 or negative.
    if ((int) $arguments['width'] <= 0) {
      throw new \InvalidArgumentException("Invalid width ('{$arguments['width']}') specified for the image 'iek_image_resize' operation");
    }

    // Fail when height is 0 or negative.
    if ((int) $arguments['height'] <= 0) {
      throw new \InvalidArgumentException("Invalid height ('{$arguments['height']}') specified for the image 'iek_image_resize' operation");
    }

    // Fail when X is negative.
    if ((int) $arguments['x'] < 0) {
      throw new \InvalidArgumentException("Invalid X-axis ('{$arguments['x']}') specified for the image 'iek_image_resize' operation");
    }

    // Fail when Y is negative.
    if ((int) $arguments['y'] < 0) {
      throw new \InvalidArgumentException("Invalid Y-axis ('{$arguments['y']}') specified for the image 'iek_image_resize' operation");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    $data = $arguments;

    if ($data['width'] < 0 || $data['height'] < 0) {
      return TRUE;
    }

    $width = $data['width'];
    $height = $data['height'];
    $blank_margin = $data['blank_margin'];
    $blank_margin_bg_color = !empty($data['blank_margin_bg_color']) ? $data['blank_margin_bg_color'] : '#ffffff';
    $position = $data['position'];

    $dst = imagecreatetruecolor($width, $height);
    $bg_rgb = iek_hex2rgb($blank_margin_bg_color);
    $bg = imagecolorallocate($dst, $bg_rgb['red'], $bg_rgb['green'], $bg_rgb['blue']);
    imagefilledrectangle($dst, 0, 0, $width, $height, $bg);

    if ($blank_margin) {
      $this->getToolkit()->apply('scale', [
        'width' => $width,
        'height' => $height,
        'upscale' => TRUE,
      ]);
    }
    else {
      $src_ratio = round($this->getToolkit()->getWidth() / $this->getToolkit()
          ->getHeight(), 8);
      $dst_ratio = round($width / $height, 8);

      if ($src_ratio >= 1) {
        if ($dst_ratio >= 1) {
          if ($src_ratio > $dst_ratio) {
            $scaled_height = $width;
            $scaled_width = $src_ratio * $width;
          }
          else {
            $scaled_width = $width;
            $scaled_height = $width / $src_ratio;
          }
        }
        else {
          $scaled_height = $height;
          $scaled_width = $src_ratio * $height;
        }
      }
      else {
        if ($dst_ratio >= 1) {
          $scaled_width = $width;
          $scaled_height = $width / $src_ratio;
        }
        else {
          if ($src_ratio < $dst_ratio) {
            $scaled_width = $height;
            $scaled_height = $height / $src_ratio;
          }
          else {
            $scaled_height = $height;
            $scaled_width = $height * $src_ratio;
          }
        }
      }

      $this->getToolkit()->apply('scale', [
        'width' => $scaled_width,
        'height' => $scaled_height,
        'upscale' => TRUE,
      ]);
    }

    switch ($position) {
      case 'coordinate':
        $src_x = $data['x'];
        $src_y = $data['y'];
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'top_left':
        $src_x = 0;
        $src_y = 0;
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'top_center':
        $src_x = -(($this->getToolkit()->getWidth() - $width) / 2);
        $src_y = 0;
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'top_right':
        $src_x = -($this->getToolkit()->getWidth() - $width);
        $src_y = 0;
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'middle_left':
        $src_x = 0;
        $src_y = -(($this->getToolkit()->getHeight() - $height) / 2);
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'middle_center':
        $src_x = -(($this->getToolkit()->getWidth() - $width) / 2);
        $src_y = -(($this->getToolkit()->getHeight() - $height) / 2);
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'middle_right':
        $src_x = -($this->getToolkit()->getWidth() - $width);
        $src_y = -(($this->getToolkit()->getHeight() - $height) / 2);
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'bottom_left':
        $src_x = 0;
        $src_y = -($this->getToolkit()->getHeight() - $height);
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'bottom_center':
        $src_x = -(($this->getToolkit()->getWidth() - $width) / 2);
        $src_y = -($this->getToolkit()->getHeight() - $height);
        $dst_x = 0;
        $dst_y = 0;
        break;

      case 'bottom_right':
        $src_x = -($this->getToolkit()->getWidth() - $width);
        $src_y = -($this->getToolkit()->getHeight() - $height);
        $dst_x = 0;
        $dst_y = 0;
        break;

      default:
        $src_x = 0;
        $src_y = 0;
        $dst_x = 0;
        $dst_y = 0;
    }

    if (!imagecopy($dst, $this->getToolkit()
      ->getResource(), $src_x, $src_y, $dst_x, $dst_y, $this->getToolkit()
      ->getWidth(), $this->getToolkit()->getHeight())
    ) {
      return FALSE;
    }

    imagedestroy($this->getToolkit()->getResource());

    // Update image object.
    $this->getToolkit()->setResource($dst);

    return TRUE;
  }

}
