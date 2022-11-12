<?php 

//This dog simple command line utility creates keyframe comparisons for use with Stable Diffusion and Ebsyth sets.
//Sometimes you have to eyeball the SD output between the source images and the redraws.
//That's what this little utility helps you do. 

//There's a good possibility you'll need to adjust the zero padding in the file names for this to work.
//But that's going to depend on your configuration, and how your file names are made, vs. what you're doing with them.

//Outputs: The simplest possible html file containing a table, and three columns of images.
//Run this after you've run Stable Defusion on an Image Sequence, after you've upscaled the sequence to your out directory.


$files = __DIR__. '/src';
$list = scandir($files);
$string = "";

$main = [];
$i=1; //used to guess the file names that stable diffusion and Ebsyth will generate. Do not change

//Ebsyth may require 5 places, but if you're not using Film instead of ebsynth, you might want to adjust to 3 spaces and 4 spaces respectively.

$spacesForID = "%04d"; //four places
$spacesForOut = "%05d"; //five places

//Adjust with all the crazy naming and preperation we need to do this.

foreach ($list as $file){
	if(strlen($file) > 2){
		$main[] = ['id'=>sprintf($spacesForID,$i),'out'=>sprintf($spacesForOut,$i-1).'.png','fud'=>base64_encode($file).'.png','file'=>$file];
		++$i;
	}
}

$renamer = $main;

//Do a saftey step first to prevent files from conflicting on the first iteration.
//Without this, or something like it, files in your first row will get overwritten with later files.

foreach ($renamer as  $file){
	if(strlen($file['file']) > 3){
		rename(__DIR__."/out/".$file['out'], __DIR__."/out/".$file['fud']);
		
	}
}
//Do the final rename
foreach ($renamer as  $file){
	if(strlen($file['file']) > 3){
		rename(__DIR__."/out/".$file['fud'], __DIR__."/out/".$file['file']);
		
	}
}

//Make some boring ol' html

foreach ($main as  $file){
	if(strlen($file['file']) > 3){
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

