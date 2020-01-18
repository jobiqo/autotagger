<?php


namespace Drupal\feeds_autotagger\Autotagger;

/**
 * Autotagger interface to implement classes that auto tags given texts.
 *
 * @package Drupal\feeds_autotagger\Autotagger
 */
interface AutotaggerInterface {

  /**
   * Tags the given text.
   *
   * @param string $text
   *
   * @return array
   */
  public function tagText(string $text): array;
}
