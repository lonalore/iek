<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImageFilter.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Filter operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_filter",
 *   toolkit = "gd",
 *   operation = "iek_image_filter",
 *   label = @Translation("IEK - Filter"),
 *   description = @Translation("Apply a filter to an image.")
 * )
 */
class ImageFilter extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'filter_name' => [
        'description' => 'Filter name',
      ],
      'repeat' => [
        'description' => 'Repeat',
      ],
      'arg1' => [
        'description' => 'arg1',
      ],
      'arg2' => [
        'description' => 'arg2',
      ],
      'arg3' => [
        'description' => 'arg3',
      ],
      'arg4' => [
        'description' => 'arg4',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Fail when filter name is empty.
    if (empty($arguments['filter_name'])) {
      throw new \InvalidArgumentException("Invalid filter name ('{$arguments['filter_name']}') specified for the image 'iek_image_filter' operation");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    $data = $arguments;

    $filter_name = $data['filter_name'];
    $repeat = isset($data['repeat']) ? $data['repeat'] : 1;
    $arg1 = $data['arg1'];
    $arg2 = $data['arg2'];
    $arg3 = $data['arg3'];
    $arg4 = $data['arg4'];

    switch ($filter_name) {
      case IMG_FILTER_BRIGHTNESS:
      case IMG_FILTER_CONTRAST:
      case IMG_FILTER_SMOOTH:
        for ($i = 0; $i < $repeat; $i++) {
          imagefilter($this->getToolkit()->getResource(), $filter_name, $arg1);
        }
        break;

      case IMG_FILTER_PIXELATE:
        for ($i = 0; $i < $repeat; $i++) {
          imagefilter($this->getToolkit()
            ->getResource(), $filter_name, $arg1, $arg2);
        }
        break;

      case IMG_FILTER_COLORIZE:
        for ($i = 0; $i < $repeat; $i++) {
          imagefilter($this->getToolkit()
            ->getResource(), $filter_name, $arg1, $arg2, $arg3, $arg4);
        }
        break;

      default:
        for ($i = 0; $i < $repeat; $i++) {
          imagefilter($this->getToolkit()->getResource(), $filter_name);
        }
        break;
    }

    return TRUE;
  }

}
