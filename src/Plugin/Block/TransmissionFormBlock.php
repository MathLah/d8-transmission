<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 18/03/2017
 * Time: 23:24
 */

namespace Drupal\transmission\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'TransmissionForm' Block.
 *
 * @Block(
 *   id = "TransmissionFormBlock",
 *   admin_label = @Translation("Transmission Form"),
 * )
 */
class TransmissionFormBlock extends BlockBase
{

    public function build() {
        $uploadForm = \Drupal::formBuilder()->getForm('Drupal\transmission\Form\UploadForm');
        return $uploadForm;
    }
}