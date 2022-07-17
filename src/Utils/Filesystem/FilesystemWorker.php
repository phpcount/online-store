<?php

namespace App\Utils\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

class FilesystemWorker
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function createFolderIfItNotExists(string $folder)
    {
        if (!$this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
    }

    public function remove(string $file): self
    {
        if ($this->filesystem->exists($file)) {
            $this->filesystem->remove($file);
        }

        return $this;
    }
}
