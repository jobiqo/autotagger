<?php


namespace Drupal\feeds_autotagger\Controller;


class FeedsAutotagger {
  const WORD_SPLIT_REGEX = '\pC\pM\pP\pZ';

  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => t('Hello World!'),
    ];
  }

  /**
   * Splits a given text into separate words.
   *
   * @param string $term
   *   The UTF-8 encoded text to split.
   *
   * @return array
   *   The resulting array containing the words only.
   */
  public function splitText(string $term) : array {
    // We want to split around words, symbols and numbers, therefore we use
    // unicode character properties to detect marks, punctuation, separators and
    // other characters we want to ignore.
    // See http://www.php.net/manual/en/regexp.reference.unicode.php
    // Filter out any remaining empty strings after the split.
    return preg_split("/[" . self::WORD_SPLIT_REGEX . "]+/u", $term, -1, PREG_SPLIT_NO_EMPTY);
  }
}
