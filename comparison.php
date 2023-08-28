<?php

class KeyframeComparisonGenerator
{
    private string $srcDirectory;
    private string $outDirectory;

    public function __construct(string $srcDirectory, string $outDirectory)
    {
        $this->srcDirectory = $srcDirectory;
        $this->outDirectory = $outDirectory;
    }

    public function generateComparison()
    {
        if (!$this->isDirectoryExists($this->srcDirectory) || !$this->isDirectoryExists($this->outDirectory)) {
            echo "Source or output directory doesn't exist. Please check the directory paths and try again.";
            exit;
        }

        $list = scandir($this->srcDirectory);
        $string = "";

        $main = [];
        $i = 1; //used to guess the file names that stable diffusion and Ebsyth will generate. Do not change

        //Ebsyth may require 5 places, but if you're not using Film instead of ebsynth, you might want to adjust to 3 spaces and 4 spaces respectively.

        $spacesForID = "%04d"; //four places
        $spacesForOut = "%05d"; //five places

        //Adjust with all the crazy naming and preparation we need to do this.

        foreach ($list as $file) {
            if (strlen($file) > 2) {
                $main[] = ['id' => sprintf($spacesForID, $i), 'out' => sprintf($spacesForOut, $i - 1) . '.png', 'fud' => base64_encode($file) . '.png', 'file' => $file];
                ++$i;
            }
        }

        $renamer = $main;

        //Do a safety step first to prevent files from conflicting on the first iteration.
        //Without this, or something like it, files in your first row will get overwritten with later files.

        foreach ($renamer as $file) {
            if (strlen($file['file']) > 3) {
                rename($this->outDirectory . "/" . $file['out'], $this->outDirectory . "/" . $file['fud']);
            }
        }

        //Do the final rename
        foreach ($renamer as $file) {
            if (strlen($file['file']) > 3) {
                rename($this->outDirectory . "/" . $file['fud'], $this->outDirectory . "/" . $file['file']);
            }
        }

        //Make some boring ol' html

        foreach ($main as $file) {
            if (strlen($file['file']) > 3) {
                $string .= "
                <tr>
                <td>{$file['id']}</td><td>{$file['file']}</td>
                <td><img src='src/{$file['file']}' width='250'></td>
                <td><img src='prep/{$file['file']}' width='250'></td>
                <td><img src='out/{$file['file']}' width='250'></td>
                </tr>";
            }
        }

        $string = "<html><head>\n<title>Generation Keyframes Comparison</title>\n</head>\n<body>\n<table border='1'>{$string}</table></body>\n</html>";

        //Create our file
        $newfile = __DIR__ . '/comparison.html';
        file_put_contents($newfile, $string);
    }

    private function isDirectoryExists(string $directory): bool
    {
        return is_dir($directory);
    }
}

$srcDirectory = __DIR__ . '/src';
$outDirectory = __DIR__ . '/out';

$generator = new KeyframeComparisonGenerator($srcDirectory, $outDirectory);
$generator->generateComparison();
