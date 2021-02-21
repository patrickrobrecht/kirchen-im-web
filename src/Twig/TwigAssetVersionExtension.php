<?php

namespace KirchenImWeb\Twig;

use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class TwigAssetVersionExtension
 * @package KirchenImWeb\Twig
 *
 * Copied from https://github.com/railto/TwigAssetVersionExtension, as the package is PHP 7 only
 */
class TwigAssetVersionExtension extends AbstractExtension
{
    private string $manifest;

    public function __construct(string $manifest)
    {
        $this->manifest = $manifest;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('asset_version', [$this, 'getAssetVersion']),
        ];
    }

    public function getAssetVersion(string $filename)
    {
        if (!file_exists($this->manifest)) {
            throw new RuntimeException(sprintf('Cannot find manifest file: "%s"', $this->manifest));
        }
        $paths = json_decode(file_get_contents($this->manifest), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($paths[$filename])) {
            throw new RuntimeException(sprintf('There is no file "%s" in the version manifest!', $filename));
        }
        return $paths[$filename];
    }

    public function getName(): string
    {
        return 'asset_version';
    }
}
