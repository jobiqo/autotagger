<?php


namespace Drupal\Tests\feeds_autotagger\Unit;


use Drupal\feeds_autotagger\Autotagger\TaxonomyAutotagger;
use Drupal\Tests\UnitTestCase;


/**
 * Tests the bare autotagging functionality.
 *
 * @group feeds_autotagger.
 */
class TaxonomyAutotaggerTest extends UnitTestCase {
  public function testSplitText() {
    $term = "hello, world, .";
    $split = TaxonomyAutotagger::splitText($term);

    $this->assertEquals(['hello', 'world'], $split);
  }

  public function testZeroSplitText() {
    $term = "0 years";
    $split = TaxonomyAutotagger::splitText($term);

    $this->assertEquals(['0', 'years'], $split);
  }

  public function testCleanText() {
    $xml_text = '<![CDATA[<h1>Hello</h1>Body text here.]]>';
    $clean = TaxonomyAutotagger::cleanText($xml_text);

    $this->assertEquals('hello body text here.', $clean);
  }

  public function testBuildNamesArray() {
    $term = 'Hello, world';
    $terms = [];
    TaxonomyAutotagger::buildNamesArray($terms, $term, 1);

    $this->assertEquals(1, $terms['hello'][0]['tid']);
    $this->assertTrue($terms['hello'][0]['splitted']);
    $this->assertEquals(mb_strtolower($term), $terms['hello'][0]['original_term_name']);

    $this->assertEquals(1, $terms['world'][0]['tid']);
    $this->assertTrue($terms['world'][0]['splitted']);
    $this->assertEquals(mb_strtolower($term), $terms['world'][0]['original_term_name']);
  }
}
