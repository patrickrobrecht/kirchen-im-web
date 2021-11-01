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
    private array $paths;

    public function __construct(string $manifest)
    {
        $this->manifest = $manifest;
        if (!file_exists($this->manifest)) {
            throw new RuntimeException(sprintf('Cannot find manifest file: "%s"', $this->manifest));
        }
        $this->paths = json_decode(file_get_contents($this->manifest), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('asset_version', [$this, 'getAssetVersion']),
        ];
    }

    public function getAssetVersion(string $filename)
    {
        if (!isset($this->paths[$filename])) {
            throw new RuntimeException(sprintf('There is no file "%s" in the version manifest!', $filename));
        }
        return '/assets' . $this->paths[$filename];
    }

    public function getName(): string
    {
        return 'asset_version';
    }
}
