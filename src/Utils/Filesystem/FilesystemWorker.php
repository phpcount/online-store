<?php

namespace App\Utils\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

class FilesystemWorker
{

    /**
     *
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     *
     * @param string $folder
     * @return void
     */
    public function createFolderIfItNotExists(string $folder)
    {
        if(!$this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
    }

    /**
     *
     * @param string $file
     * @return self
     */
    public function remove(string $file): self
    {
        if ($this->filesystem->exists($file)) {
            $this->filesystem->remove($file);
        }

        return $this;
    }
}
