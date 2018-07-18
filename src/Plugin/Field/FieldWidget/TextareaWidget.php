<?php

namespace Drupal\sshkey\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'sshkey_textarea' field widget.
 *
 * @FieldWidget(
 *   id = "sshkey_textarea",
 *   label = @Translation("Textarea"),
 *   field_types = {"sshkey_default"},
 * )
 */
class TextareaWidget extends StringTextareaWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $items[$delta]->name,
      '#weight' => -1,
    ];
    return $element;
  }

}
