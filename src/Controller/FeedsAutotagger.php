<?php


namespace Drupal\feeds_autotagger\Controller;


class FeedsAutotagger {
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => t('Hello World!'),
    ];
  }
}
