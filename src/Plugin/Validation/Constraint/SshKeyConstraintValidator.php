<?php

namespace Drupal\sshkey\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates public ssh key.
 */
class SshKeyConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    try {
      list($algorithm, $key, $comment) = array_pad(explode(' ', $value, 3), 3, NULL);
      // @todo: Validate algorithm.
      if (!in_array($algorithm, array_filter($constraint->algorithm))) {
        throw new \Exception('Invalid algorithm');
      }
      // Validate if key can be decoded.
      $key_base64_decoded = base64_decode($key, TRUE);
      if ($key_base64_decoded === FALSE) {
        throw new \Exception('The key could not be decoded.');
      }
      // Validate format.
      $expected_prefix = pack('N', strlen($algorithm)) . $algorithm;
      if (strpos($key_base64_decoded, $expected_prefix) !== 0) {
        throw new \Exception('The key is invalid. It is not a public key.');
      }
    }
    catch (\Exception $e) {
      $this->context->addViolation($constraint->message);
    }
  }

}
