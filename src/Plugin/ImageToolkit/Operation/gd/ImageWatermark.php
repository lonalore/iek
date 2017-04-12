<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImageWatermark.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Watermark operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_watermark",
 *   toolkit = "gd",
 *   operation = "iek_image_watermark",
 *   label = @Translation("IEK - Watermark"),
 *   description = @Translation("Add a watermark text on an image.")
 * )
 */
class ImageWatermark extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return array(
      'text'           => array(
        'description' => 'Text',
      ),
      'font'           => array(
        'description' => 'Font',
      ),
      'color'          => array(
        'description' => 'Color',
      ),
      'size'           => array(
        'description' => 'Size',
      ),
      'angle'          => array(
        'description' => 'Angle',
      ),
      'position'       => array(
        'description' => 'Position',
      ),
      'padding_top'    => array(
        'description' => 'Padding top',
      ),
      'padding_right'  => array(
        'description' => 'Padding right',
      ),
      'padding_bottom' => array(
        'description' => 'Padding bottom',
      ),
      'padding_left'   => array(
        'description' => 'Padding left',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    if (empty($arguments['text'])) {
      throw new \InvalidArgumentException("Invalid text ('{$arguments['text']}') specified for the image 'iek_image_watermark' operation");
    }

    if (empty($arguments['font'])) {
      throw new \InvalidArgumentException("Invalid font ('{$arguments['font']}') specified for the image 'iek_image_watermark' operation");
    }

    // TODO - validate color code.

    if ((int) $arguments['size'] <= 0) {
      throw new \InvalidArgumentException("Invalid size ('{$arguments['size']}') specified for the image 'iek_image_watermark' operation");
    }

    if (!is_numeric($arguments['angle'])) {
      throw new \InvalidArgumentException("Invalid angle ('{$arguments['angle']}') specified for the image 'iek_image_watermark' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_top'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_top']}') specified for the image 'iek_image_watermark' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_right'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_right']}') specified for the image 'iek_image_watermark' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_bottom'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_bottom']}') specified for the image 'iek_image_watermark' operation");
    }

    // Fail when padding is negative.
    if ((int) $arguments['padding_left'] < 0) {
      throw new \InvalidArgumentException("Invalid padding ('{$arguments['padding_left']}') specified for the image 'iek_image_watermark' operation");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = array()) {
    $data = $arguments;

    $width = $this->getToolkit()->getWidth();
    $height = $this->getToolkit()->getHeight();

    $text = $data['text']; // TODO - token replace.
    $angle = 360 - $data['angle'];
    $size = $data['size'];
    $position = $data['position'];
    $padding_top = $data['padding_top'];
    $padding_right = $data['padding_right'];
    $padding_bottom = $data['padding_bottom'];
    $padding_left = $data['padding_left'];

    $iek_font = iek_get_watermark_fonts($data['font']);
    $font = \Drupal::service('file_system')
      ->realpath($iek_font['path'] . '/' . $iek_font['file']);

    $dst = imagecreatetruecolor($width, $height);
    $text_rgb = iek_hex2rgb($data['color']);
    $text_color = imagecolorallocate($dst, $text_rgb['red'], $text_rgb['green'], $text_rgb['blue']);

    // Wraps the watermark text.
    $bbox = imagettfbbox($size, $angle, $font, $text);
    $bbox_width = abs($bbox[2] - $bbox[0]);
    $bbox_height = abs($bbox[5] - $bbox[3]);
    $bbox_character_width = 0;
    if (strlen($text)) {
      $bbox_character_width = ceil($bbox_width / strlen($text));
    }
    $bbox_character_height = ceil($bbox_height);

    $text_arr = array();
    if ($bbox_character_width) {
      $text_arr = chunk_split($text, ceil(($width - abs(($padding_left + $padding_right) * 2)) / $bbox_character_width), ':::');
      $text_arr = explode(':::', $text_arr);
    }

    switch ($position) {
      case 'top_left':
        $new_y = $bbox_character_height + $padding_top;
        foreach ($text_arr as $text_arr_item) {
          $new_x = $padding_left;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'top_center':
        $new_y = $bbox_character_height + $padding_top;
        foreach ($text_arr as $text_arr_item) {
          $new_x = ($width - $bbox_character_width * strlen($text_arr_item)) / 2;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'top_right':
        $new_y = $bbox_character_height + $padding_top;
        foreach ($text_arr as $text_arr_item) {
          $cur_bbox = imagettfbbox($size, $angle, $font, $text_arr_item);
          $cur_bbox_width = abs($cur_bbox[2] - $cur_bbox[0]);
          $new_x = $width - $cur_bbox_width - $padding_right;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'middle_left':
        $new_y = ($height / 2 - $bbox_character_height * count($text_arr) / 2 + $bbox_character_height);
        foreach ($text_arr as $text_arr_item) {
          $new_x = $padding_left;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'middle_center':
        $new_y = ($height / 2 - $bbox_character_height * count($text_arr) / 2 + $bbox_character_height);
        foreach ($text_arr as $text_arr_item) {
          $new_x = ($width - $bbox_character_width * strlen($text_arr_item)) / 2;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'middle_right':
        $new_y = ($height / 2 - $bbox_character_height * count($text_arr) / 2 + $bbox_character_height);
        foreach ($text_arr as $text_arr_item) {
          $cur_bbox = imagettfbbox($size, $angle, $font, $text_arr_item);
          $cur_bbox_width = abs($cur_bbox[2] - $cur_bbox[0]);
          $new_x = $width - $cur_bbox_width - $padding_left;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'bottom_left':
        $new_y = $height - $bbox_character_height * count($text_arr) - $padding_bottom + $bbox_character_height * 2;
        foreach ($text_arr as $text_arr_item) {
          $new_x = $padding_left;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'bottom_center':
        $new_y = $height - $bbox_character_height * count($text_arr) - $padding_bottom + $bbox_character_height * 2;
        foreach ($text_arr as $text_arr_item) {
          $new_x = ($width - $bbox_character_width * strlen($text_arr_item)) / 2;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      case 'bottom_right':
        $new_y = $height - $bbox_character_height * count($text_arr) - $padding_bottom + $bbox_character_height * 2;
        foreach ($text_arr as $text_arr_item) {
          $cur_bbox = imagettfbbox($size, 0, $font, $text_arr_item);
          $cur_bbox_width = abs($cur_bbox[2] - $cur_bbox[0]);
          $new_x = $width - $cur_bbox_width - $padding_left;
          imagettftext($this->getToolkit()
            ->getResource(), $size, $angle, $new_x, $new_y, $text_color, $font, $text_arr_item);
          $new_y += $bbox_character_height;
        }
        break;

      default:
        $new_x = 0;
        $new_y = 0;
    }

    return TRUE;
  }

}
