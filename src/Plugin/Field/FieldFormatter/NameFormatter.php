<?php

namespace Drupal\sshkey\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Name' formatter.
 *
 * @FieldFormatter(
 *   id = "sshkey_name",
 *   label = @Translation("Name"),
 *   field_types = {
 *     "sshkey_default"
 *   }
 * )
 */
class NameFormatter extends FingerprintFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $settings = $this->getSettings();

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#type' => 'item',
        '#markup' => $item->name ?: $item->fingerprint,
        '#field_prefix' => $settings['prefix'],
        '#field_suffix' => $settings['suffix'],
      ];
    }

    return $element;
  }

}
