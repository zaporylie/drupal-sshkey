<?php

namespace Drupal\sshkey;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * SshkeyBreakdown service.
 */
class Utils {

  private $algorithm;
  private $key;
  private $comment;

  private function __construct($algorithm, $key, $comment = null) {
    $this->algorithm = $algorithm;
    $this->key = $key;
    $this->comment = $comment;
  }

  /**
   * Retrieves the last created node.
   */
  public static function initialize($value) {
    list($algorithm, $key, $comment) = array_pad(explode(' ', $value, 3), 3, NULL);
    return new static($algorithm, $key, $comment);
  }

  public function getAlgorithm() {
    return $this->algorithm;
  }

  public function getKey() {
    return $this->key;
  }

  public function getComment() {
    return $this->comment;
  }

  public function getFingerprintMd5() {
    return md5(base64_decode($this->key, TRUE));
  }

  public function getFingerprintSha256() {
    return base64_encode(hash('sha256', base64_decode($this->key), TRUE));
  }

}
