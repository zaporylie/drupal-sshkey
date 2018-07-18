<?php

namespace Drupal\sshkey\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'sshkey_default' field type.
 *
 * @FieldType(
 *   id = "sshkey_default",
 *   label = @Translation("SSH Key"),
 *   category = @Translation("General"),
 *   default_widget = "sshkey_textarea",
 *   default_formatter = "sshkey_fingerprint"
 * )
 */
class SshKeyItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'algorithm' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    $element['algorithm'] = [
      '#required' => TRUE,
      '#type' => 'checkboxes',
      '#title' => $this->t('Algorithm'),
      '#default_value' => $settings['algorithm'],
//      '#multiple' => TRUE,
      '#options' => [
        'ssh-rsa' => 'ssh-rsa',
        'ssh-dss' => 'ssh-dss',
        'ssh-ed25519' => 'ssh-ed25519',
      ],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    if (isset($values) && !is_array($values)) {
      // If either a scalar or an object was passed as the value for the item,
      // assign it to the 'entity' property since that works for both cases.
      $this->set('value', $values, $notify);
    }
    else {
      parent::setValue($values, FALSE);
      // Support setting the field item with only one property, but make sure
      // values stay in sync if only property is passed.
      // NULL is a valid value, so we use array_key_exists().
      if (is_array($values) && array_key_exists('value', $values) && !isset($values['fingerprint'])) {
        $this->onChange('value', FALSE);
      }
      // Notify the parent if necessary.
      if ($notify && $this->getParent()) {
        $this->getParent()->onChange($this->getName());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name, $notify = TRUE) {
    if ($property_name == 'value') {
      $property = $this->get('value')->getValue();
      // @todo: Get an actual fingerprint.
      $this->writePropertyValue('fingerprint', md5($property));
      if (!$this->get('name')->getValue()) {
        // Get comment from the key.
        $this->writePropertyValue('name', md5($property));
      }
    }
    parent::onChange($property_name, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('SSH key'))
      ->setDescription(t('The raw key value.'))
      ->setRequired(TRUE);

    $properties['fingerprint'] = DataDefinition::create('string')
      ->setLabel(t('Fingerprint'))
      ->setDescription(t('The unique fingerprint (MD5 hash) of the key.'))
      ->setRequired(TRUE)
      ->setInternal(TRUE);

    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The nickname of the SSH key.'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();

    // @DCG Suppose our value must not be longer than 10 characters.
    $options['name']['Length']['max'] = 128;
    $options['value']['SshKey']['algorithm'] = $this->getFieldDefinition()->getSetting('algorithm');

    // @DCG
    // See /core/lib/Drupal/Core/Validation/Plugin/Validation/Constraint
    // directory for available constraints.
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'value' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'The raw key value.',
        'length' => 255,
      ],
      'fingerprint' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'The unique fingerprint (MD5 hash) of the key.',
        'length' => 64,
      ],
      'name' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'The nickname of the SSH key.',
        'length' => 128,
      ],
    ];

    $schema = [
      'columns' => $columns,
      'unique keys' => [],
      'indexes' => [
        'fingerprint' => ['fingerprint'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, 50));
    $values['fingerprint'] = $random->word(mt_rand(1, 50));
    $values['name'] = $random->word(mt_rand(1, 50));
    return $values;
  }

}
