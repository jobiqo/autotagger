<?php


namespace Drupal\feeds_autotagger\Services;

/**
 * Autotags texts by matching similar terms.
 *
 * @todo: find a better name.
 *
 * @package Drupal\feeds_autotagger\Services
 *
 * @AutotaggerPlugin
 */
class AutotaggerService {
  const WORD_SPLIT_REGEX = '\pC\pM\pP\pZ';

  /**
   * @var
   */
  protected $vocabulary;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct(\Drupal\Core\Database\Connection $database) {
    //@todo: replace with DI.
    $this->database = $database;
    $this->vocabulary = '';
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
  public static function splitText(string $term) : array {
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
  public static function cleanText(string $text) : string {
    $text = preg_replace('/<!\[CDATA\[(.*?)\]\]>/is', '$1', $text);
    // strip_tags() does not introduce any white space for concatenated tags.
    // Example: "<h1>Hello</h1>Body text here." would become "HelloBody text here."
    $text = preg_replace('/(<[^>]+>)/', '$1 ', $text);
    $text = strip_tags($text);
    $text = mb_strtolower($text);
    $text = trim($text);
    return $text;
  }

  /**
   * Helper function for generating the term names array.
   *
   * @param array $terms
   *   An array of terms, where the new name is going to be added.
   * @param string $name
   *   The name (term name, synonym, ...) to add.
   * @param int $tid
   *   The corresponding term id.
   */
  public static function buildNamesArray(array &$terms, string $name, int $tid) {
    $name = self::cleanText($name);
    $name_splits = self::splitText($name);

    foreach ($name_splits as $split) {
      $terms[$split][] = [
        'tid' => $tid,
        'splitted' => (count($name_splits) > 1) ? TRUE : FALSE,
        'original_term_name' => $name,
      ];
    }
  }

  /**
   * {@inheritDoc}
   */
  public function tagText(string $text): array {
    $extracted_tids = [];

    $terms = $this->getTermNames($this->vocabulary);

    $text = self::cleanText($text);
    $text_tokens = array_flip(self::cleanText($text));

    $matchings_term_splits = array_intersect_key($terms, $text_tokens);

    // Loops over all matched splits and checks if a term name consists
    // of multiple splits. If so, an additional text parsing for the whole
    // term name is performed.
    foreach ($matchings_term_splits as $results) {
      foreach ($results as $result) {
        $tid = $result['tid'];
        if (!in_array($tid, $extracted_tids)) {
          if ($result['splitted']) {
            // The compound word needs to be at the start or end of the text or
            // separated by split characters form the other text.
            if (preg_match('/(^|[' . RULES_AUTOTAG_SPLIT_REGEX . ']+)' . preg_quote($result['original_term_name'], '/') . '($|[' . RULES_AUTOTAG_SPLIT_REGEX . ']+)/ui', $text)) {
              $extracted_tids[] = $tid;
            }
          }
          else {
            $extracted_tids[] = $tid;
          }
        }
      }
    }

    return $extracted_tids;
  }

  /**
   * Returns an array of terms, keyed by splitted term names.
   *
   * The structure can be modified with hook_rules_autotag_terms_alter()
   * implementations.
   */
  public function getTermNames(string $vocabulary) : array {
    $terms = drupal_static(__METHOD__, []);

    if (empty($terms[$vocabulary])) {
      $terms[$vocabulary] = [];

      $sql = 'SELECT name, tid FROM {taxonomy_term_field_data} AS t WHERE t.vid = :vocabulary';
      $query = $this->database->query($sql, [':vocabulary' => $vocabulary]);
      $result = $query->fetchAll();

      foreach ($result as $term) {
        $this->buildNamesArray($terms[$vocabulary], $term->name, $term->tid);
      }
    }

    return $terms[$vocabulary];
  }

}
