<?php

namespace Drupal\sshkey\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Regex constraint.
 *
 * Overrides the symfony constraint to use Drupal-style replacement patterns.
 *
 * @Constraint(
 *   id = "SshKey",
 *   label = @Translation("SSH Key", context = "Validation")
 * )
 */
class SshKeyConstraint extends Constraint {

  public $message = 'This key is not valid.';

}
