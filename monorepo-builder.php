<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;
use YourMonorepo\YourMonorepo\UpdatePackageVersionWorker;

require __DIR__.'/vendor/autoload.php';

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__.'/packages']);
    $mbConfig->defaultBranch('main');
    $mbConfig->dataToRemove(
        [
            ComposerJsonSection::REQUIRE      => [
                // the line is removed by key, so version is irrelevant, thus *
                'phpunit/phpunit' => '*',
            ],
            ComposerJsonSection::REPOSITORIES => [
                // this will remove all repositories
                Option::REMOVE_COMPLETELY,
            ],
        ]
    );

    // release workers - in order to execute
    $mbConfig->workers(
        [
            UpdateReplaceReleaseWorker::class,
            SetCurrentMutualDependenciesReleaseWorker::class,
            AddTagToChangelogReleaseWorker::class,
            TagVersionReleaseWorker::class,
            PushTagReleaseWorker::class,
            SetNextMutualDependenciesReleaseWorker::class,
            UpdateBranchAliasReleaseWorker::class,
            PushNextDevReleaseWorker::class,
            UpdatePackageVersionWorker::class,
        ]
    );
};
