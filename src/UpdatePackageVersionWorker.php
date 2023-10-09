<?php

declare (strict_types=1);

namespace YourMonorepo\YourMonorepo;

use MonorepoBuilderPrefix202308\Symplify\SmartFileSystem\SmartFileInfo;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Exception\MissingComposerJsonException;

final class UpdatePackageVersionWorker implements ReleaseWorkerInterface
{

    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider
     */
    private ComposerJsonProvider $composerJsonProvider;
    /**
     * @var \Symplify\MonorepoBuilder\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private JsonFileManager $jsonFileManager;

    public function __construct(ComposerJsonProvider $composerJsonProvider, JsonFileManager $jsonFileManager)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
    }

    public function getDescription(Version $version): string
    {
        return "Update the version in all composer.json files to the new tag";
    }

    /**
     * @throws \Symplify\MonorepoBuilder\Release\Exception\MissingComposerJsonException
     * @throws \MonorepoBuilderPrefix202308\Symplify\SymplifyKernel\Exception\ShouldNotHappenException
     */
    public function work(Version $version): void
    {
        $this->updateRootComposer($version);
        $this->updatePackageComposerJsons($version);
    }

    /**
     * @throws \Symplify\MonorepoBuilder\Release\Exception\MissingComposerJsonException
     */
    private function updateRootComposer(Version $version): void
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();
        $rootComposerJson->setVersion($version->getVersionString());
        $rootFileInfo = $rootComposerJson->getFileInfo();
        if (!$rootFileInfo instanceof SmartFileInfo) {
            throw new MissingComposerJsonException();
        }
        $this->jsonFileManager->printJsonToFileInfo($rootComposerJson->getJsonArray(), $rootFileInfo);
    }

    /**
     * @throws \MonorepoBuilderPrefix202308\Symplify\SymplifyKernel\Exception\ShouldNotHappenException
     */
    private function updatePackageComposerJsons(Version $version): void
    {
        $packageComposerJsons = $this->composerJsonProvider->getPackageComposerJsons();
        foreach ($packageComposerJsons as $packageComposerJson) {
            $packageName = $packageComposerJson->getName();
            if ($packageName === null) {
                continue;
            }
            $packageComposerJson->setVersion($version->getVersionString());
            $packageFileInfo = $this->composerJsonProvider->getPackageFileInfoByName($packageName);
            $this->jsonFileManager->printJsonToFileInfo($packageComposerJson->getJsonArray(), $packageFileInfo);
        }
    }
}