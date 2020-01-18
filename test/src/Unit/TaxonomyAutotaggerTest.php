<?php


namespace Drupal\Tests\feeds_autotagger\Unit;


use Drupal\feeds_autotagger\Controller\FeedsAutotagger;
use Drupal\Tests\UnitTestCase;


/**
 * Tests the bare autotagging functionality.
 *
 * @group feeds_autotagger.
 */
class TaxonomyAutotaggerTest extends UnitTestCase {
  public function testSplitText() {
    $autotagger = new FeedsAutotagger();

    $term = "hello, world, .";
    $split = $autotagger->splitText($term);

    $this->assertEquals(['hello', 'world'], $split);
  }

  public function testZeroSplitText() {
    $autotagger = new FeedsAutotagger();

    $term = "0 years";
    $split = $autotagger->splitText($term);

    $this->assertEquals(['0', 'years'], $split);
  }

  public function testPrepareXMlText() {
    $autotagger = new FeedsAutotagger();

    $xml_text = '<![CDATA[<h1>Hello</h1>Body text here.]]>';
    $clean = $autotagger->prepareXMLText($xml_text);

    $this->assertEquals('hello body text here.', $clean);
  }
}
