<?php

declare (strict_types=1);

namespace YourMonorepo\YourMonorepo;

use MonorepoBuilderPrefix202308\Symplify\SmartFileSystem\SmartFileInfo;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Exception\MissingComposerJsonException;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class UpdatePackageNextVersionWorker extends UpdatePackageVersionWorker
{

    public function getDescription(Version $version): string
    {
        return "Update the version in all composer.json files to the next version";
    }

    public function work(Version $version): void
    {
        parent::work($this->getNextVersion($version));
    }

    private function getNextVersion(Version $version): Version
    {
        return new Version(
            sprintf(
                '%d.%d.%d',
                $version->getMajor()->getValue() ?? 0,
                ($version->getMinor()->getValue() ?? 0) + 1,
                0
            )
        );
    }

}