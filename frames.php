<?php

class KeyFrameProcessor {
    private string $srcDirectory;
    private string $prepDirectory;
    private int $deviation;
    
    public function __construct(string $srcDirectory, string $prepDirectory, int $deviation) {
        $this->srcDirectory = $srcDirectory;
        $this->prepDirectory = $prepDirectory;
        $this->deviation = $deviation;
    }
    
    public function processFiles() {
        if (!$this->isDirectoryExists($this->srcDirectory) || !$this->isDirectoryExists($this->prepDirectory)) {
            echo "Source or output directory doesn't exist. Please check the directory paths and try again.";
            return;
        }
        
        $fileList = scandir($this->srcDirectory);
        
        $filesToMove = [];
        $fileCount = count($fileList) ?? 0;
        
        $inc = 0;
        
        foreach ($fileList as $file) {
            if (strlen($file) > 2) {
                if ($inc++ % $this->deviation === 0) { // always picks the first file, this is intentional
                    if ($this->isDirectoryExists($this->srcDirectory . '/' . $file) && strlen($file) > 3) {
                        $filesToMove[] = $file;
                        if ($this->copyFile($file)) {
                            echo "Success! We've copied {$this->srcDirectory}/{$file} into {$this->prepDirectory}/{$file}\n";
                        } else {
                            echo "Could not move {$this->srcDirectory}/{$file}\n";
                        }
                    }
                }
            }
        }
        
        echo "ta da!\n\n";
    }
    
    private function copyFile(string $file): bool {
        return copy($this->srcDirectory . '/' . $file, $this->prepDirectory . '/' . $file);
    }
    
    private function isDirectoryExists(string $directory): bool {
        return is_dir($directory);
    }
}

$src = __DIR__ . '/src';
$prep = __DIR__ . '/prep';
$deviation = 150;

$processor = new KeyFrameProcessor($src, $prep, $deviation);
$processor->processFiles();
