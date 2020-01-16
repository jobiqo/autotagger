<?php


namespace Drupal\Tests\feeds_autotagger\Unit;


use Drupal\feeds_autotagger\Controller\FeedsAutotagger;
use Drupal\Tests\UnitTestCase;


/**
 * Tests the bare autotagging functionality.
 *
 * @group feeds_autotagger.
 */
class AutotaggerTest extends UnitTestCase {
  public function testSplitText() {
    $autotagger = new FeedsAutotagger();

    $term = "hello, world, .";
    $split = $autotagger->splitText($term);

    $this->assertEquals(['hello', 'world'], $term);
  }

  public function testZeroSplitText() {
    $autotagger = new FeedsAutotagger();

    $term = "0 years";
    $split = $autotagger->splitText($term);

    $this->assertEquals(['0', 'years'], $split);
  }
}
