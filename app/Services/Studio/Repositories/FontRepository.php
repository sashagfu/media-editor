<?php

namespace App\Services\Studio\Repositories;

use App\Services\Studio\Entities\Font;
use App\Services\Studio\Entities\FontContract;
use App\Services\Studio\StudioException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FontRepository implements FontRepositoryContract
{

    /** @var string $fontsDir */
    protected $fontsDir;

    /** @var array $fonts All fonts names/keys */
    protected $fonts;

    /**
     * FontRepository constructor.
     * @throws StudioException
     */
    public function __construct()
    {
        if (config('studio.fonts_dir') && is_dir(config('studio.fonts_dir'))) {
            $this->fontsDir = config('studio.fonts_dir');
        } else {
            throw new StudioException(
                $message = 'Path to fonts is incorrect',
                $code = StudioException::FONTS_DIRECTORY_IS_INVALID
            );
        }

        $this->fonts = array_filter(array_diff(scandir($this->fontsDir), ['..', '.']), [$this, 'isValid']);
    }

    /**
     *  Get all fonts
     * @return Collection
     */
    public function all(): Collection
    {
        return collect($this->fonts)
            ->map(function ($font) {
                return Font::make($font);
            });
    }

    public function available() : array
    {
        return $this->fonts;
    }

    public function find(string $key): FontContract
    {
        try {
            return Font::make($key);
        } catch (StudioException $e) {
            return null;
        }
    }

    /**
     *  Check if directory matches the fonts autoloader rules
     * @param string $dirName
     * @return bool
     */
    protected function isValid(string $dirName)
    {
        if (!is_dir($this->fontsDir.$dirName)) {
            return false;
        }

        /** @var array $files Files inside a directory */
        $files = array_filter(scandir($this->fontsDir.$dirName), function ($child) use ($dirName) {
            return !in_array($child, ['..', '.']) && !is_dir($this->fontsDir.$dirName . '/' . $child);
        });

        /** @var array $fonts TTF fonts files */
        $fonts = array_filter($files, function ($file) use ($dirName) {
            return !! preg_match("/^{$dirName}[A-z-_]*\.ttf$/", $file);
        });

        return !! count($fonts);
    }
}
