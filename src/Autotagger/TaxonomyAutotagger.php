<?php


namespace Drupal\feeds_autotagger\Autotagger;

/**
 * Autotags texts by matching terms.
 *
 * @package Drupal\feeds_autotagger\Autotagger
 */
class TaxonomyAutotagger implements AutotaggerInterface {
  const WORD_SPLIT_REGEX = '\pC\pM\pP\pZ';

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

  /**
   * Prepares a text from an XML element so it can be split into words.
   *
   * @param string $text
   *   The text from an XML node that should be later split into a word array.
   *
   * @return string
   *   The text with all xml and html elements removed. The text is also
   *   converted to lower case.
   */
  public function prepareXMLText(string $text) : string {
    $text = preg_replace('/<!\[CDATA\[(.*?)\]\]>/is', '$1', $text);
    // strip_tags() does not introduce any white space for concatenated tags.
    // Example: "<h1>Hello</h1>Body text here." would become "HelloBody text here."
    $text = preg_replace('/(<[^>]+>)/', '$1 ', $text);
    $text = strip_tags($text);
    $text = mb_strtolower($text);
    $text = trim($text);
    return $text;
  }

  public function tagText(string $text): array {
    // TODO: Implement tagText() method.
  }

}
