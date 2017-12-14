<?php
/**
 * Load additional drushrc.php files
 */
if (file_exists(__DIR__ . '/contrib/drush-shell-aliases/drushrc.php')) {
  include __DIR__ . '/contrib/drush-shell-aliases/drushrc.php';
}

