<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace KirchenImWeb\Twig;

use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigViteAssetExtension extends AbstractExtension
{
    private array $manifest;

    public function __construct(private readonly string $manifestPath)
    {
        if (!file_exists($this->manifestPath)) {
            $this->manifest = [];
            return;
        }
        $this->manifest = json_decode(file_get_contents($this->manifestPath), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('asset_version', [$this, 'getAssetVersion']),
        ];
    }

    public function getAssetVersion(string $filename): string
    {
        if (isset($this->manifest[$filename])) {
            return '/assets/' . $this->manifest[$filename]['file'];
        }

        // Fallback if not found in manifest.
        return $filename;
    }

    public function getName(): string
    {
        return 'asset_version';
    }
}
