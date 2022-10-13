https://user-images.githubusercontent.com/6313979/195616003-5b3a7022-bd1b-49d6-ba13-f91633c21d2c.mov

# How to use

Make sure you have the latest php 8.1 installed on your system first.

The main function you are probably looking for is located in sumFiles.php and is called sumDescFiles().

To use this production ready functionality without a front-end please run in your terminal using php.

If files used for testing this function do not exist then running the file by itself will prepare sample documents for you:

`php sumFiles.php`

If there are files that exist already, and they are .txt files containing numbers and references to other files then make sure to specify the file path afterwards like so...

`php sumFiles.php D.txt`
