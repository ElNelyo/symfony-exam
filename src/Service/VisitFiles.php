<?php

namespace App\Service;
use Symfony\Component\Finder\Finder;
class File
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}

class Directory
{
    /**
     * @param string $name
     * @param (File|Directory)[] $children
     */
    public function __construct(
        public readonly string $name,
        public readonly array $children,
    ) {
    }
}

class VisitFiles
{
    /**
     * Traverse Files & Directories.
     *
     * Return a list of every files filtered by given function.
     *
     * @param String $root
     * @param callable $filterFn
     *
     * @return void
     */
    public function visitFiles($root, callable $filterFn): array
    {
        $finder = new Finder();
        $fileToReturn = [];

        foreach ($finder->in($root)->files() as $fileObject) {
            $fileObject = (object) ['name' => $fileObject->getFilename(), 'path' => $fileObject->getPathname()];
            if ($filterFn($fileObject))
                $fileToReturn[] = $fileObject;
        }
        return $fileToReturn;
    }

    public function usageExemple(): void
    {
        $this->visitFiles(
            __DIR__,
            function ($file) {
                $name = $file->name;
                for ($i = 0; $i < floor(strlen($name)); $i++) {
                    if ($name[$i] != $name[strlen($name) - $i - 1]) {
                        return false;
                    }
                }
                return true;
            }
        );
    }
}
