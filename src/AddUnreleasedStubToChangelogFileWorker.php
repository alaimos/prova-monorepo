<?php

declare (strict_types=1);

namespace YourMonorepo\YourMonorepo;

use MonorepoBuilderPrefix202308\Symplify\SmartFileSystem\SmartFileSystem;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

use function array_filter;
use function array_map;
use function array_slice;
use function count;
use function explode;
use function file_exists;
use function getcwd;
use function sprintf;
use function str_replace;
use function trim;

final class AddUnreleasedStubToChangelogFileWorker implements ReleaseWorkerInterface
{

    private const UNRELEASED_STUB = "\n## Unreleased\n\n<!-- automatic release commit placeholder == DO NOT REMOVE == -->\n";

    public function __construct(private readonly SmartFileSystem $smartFileSystem) {}

    public function getDescription(Version $version): string
    {
        return "Add the \"Unreleased\" section stub to the CHANGELOG.md file";
    }

    public function work(Version $version): void
    {
        $changelogFilePath = getcwd().'/CHANGELOG.md';
        if (!file_exists($changelogFilePath)) {
            return;
        }
        $changelogFileContent = $this->smartFileSystem->readFile($changelogFilePath);
        $sections = explode("\n##", $changelogFileContent, 2);
        $sections[0] .= self::UNRELEASED_STUB;
        $changelogFileContent = implode("\n##", $sections);
        $this->smartFileSystem->dumpFile($changelogFilePath, $changelogFileContent);
    }

}