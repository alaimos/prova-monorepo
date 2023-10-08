<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;


return static function (MBConfig $mbConfig): void {
  $mbConfig->packageDirectories([__DIR__ . '/packages']);
  $mbConfig->dataToRemove([
    ComposerJsonSection::REQUIRE => [
      // the line is removed by key, so version is irrelevant, thus *
      'phpunit/phpunit' => '*',
    ],
    ComposerJsonSection::REPOSITORIES => [
      // this will remove all repositories
      Option::REMOVE_COMPLETELY,
    ],
  ]);
};
