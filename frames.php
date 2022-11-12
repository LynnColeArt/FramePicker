<?php 

//This one is a command line utility to pull key frames from your input directory directory, into your prep directory,
//where you can use stable diffusion to redraw said frames, which go in your output directory. 

//This file does not execute stable diffusion.
//It's just a pre-processor. Relax, okay?

$files = __DIR__. '/src'; 	//the raw images from your video sequence
$mover = __DIR__. '/prep'; 	//the ones you want SD to redraw
$deviation = 150; 			//how often you want sd to redraw one of these images 
$list = scandir($files);

$filesToMove = [];
$fileCount = count($list);

$inc = 0;

foreach ($list as  $file){
	if(strlen($file) > 2){
		if($inc++ % $deviation == 0){ //always picks the first file, this is intentional
			if( file_exists( $files . '/'.$file) && strlen($file) > 3){
				$filesToMove[] = $file;
				if(copy($files.'/'.$file, $mover. '/'.$file)){
					echo "Success! We've copied {$files}/($file} into {$mover}/{$file}\n";
				}
				else{
					echo "Could not move {$files}/($file}\n";
				}
			}
		}
	}
	
}

echo "ta da!\n\n";
