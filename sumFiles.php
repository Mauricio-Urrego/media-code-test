<?php
class sumFiles {
	// The following comes from the command line argv input (only reads first parameter).
	private string $startFileInput;
	// The following is used to read the files line by line. But could be changed to commas for example.
	const DELIMITER = "\n";
	// The following can be expanded on for example by adding txt|DOCX|DOC|doc etc.
	const FILETYPES = "/^.*\.(txt)$/";

	public function __construct($startFileInput = '') {
		$this->startFileInput = $startFileInput;
		if (!$startFileInput) {
			// In case no file is referenced it will create files for you and randomly select one.
			$result = $this->createFiles(10, 5);
			if ($result[0]) {
				echo "Created " . $result[0] . " new files with " . $result[1] . " random numbers in each file. \n";
				echo "Using random file out of the " . $result[0] . " as the starting point. \n";
				echo "Add a parameter to use a specific starting file path. (e.g. 'php sumFiles.php E.txt') \n";
				$randomStartFileLetter = $result[2][mt_rand(0, $result[0])];
				$this->startFileInput = $randomStartFileLetter . '.txt';
			}
		}
		if (!file_exists($this->startFileInput)) {
			exit("File does not exist. Please enter a valid file path (e.g. 'B.txt' or '~/documents/C.txt').\n");
		}
		echo 'Starting file path is: ' . $this->startFileInput . "\n";
	}

	/**
	 *
	 *       ccee88oo
	 *    C8O8O8Q8PoOb o8oo
	 *   dOB69QO8PdUOpugoO9bD
	 *  CgggbU8OU qOp qOdoUOdcb
	 *      6OuU  /p u gcoUodpP
	 *       \\\//  /douUP
	 *        \\\////
	 *        |||/\            Given a file path it will...
	 *        |||\/         add together the numbers listed in that file...
	 *        |||||        and any numbers referenced in other files.
	 *  .....//||||\....
	 *
	 * @return float|bool
	 */
	public function sumDescFiles(): float|bool {
		$fileContent = $this->getFileContents($this->startFileInput);
		$sum = array_sum($fileContent);
		$filesReferenced = preg_grep(self::FILETYPES, $fileContent);

		if ($filesReferenced) {
			$needToCheckReferences = true;
			while ($needToCheckReferences) {
				$result = $this->checkForReferences($sum, $filesReferenced);
				$sum = $result[0];
				if ($result[1]) {
					$filesReferenced = $result[1];
				}
				else {
					$needToCheckReferences = false;
				}
			}
		}

		return $sum;
	}

	/**
	 *
	 * Helper function for sumDescFiles() used for organizing file content into array.
	 * This is based on the new line ("\n") delimiter but can always be expanded on or updated.
	 *
	 * @param $fileName
	 * @return array
	 */
	private function getFileContents($fileName):array {
		$file = file_get_contents($fileName);
		return explode(self::DELIMITER, $file);
	}

	/**
	 *
	 * Helper function for sumDescFiles() used for finding any file references and adding their numbers into the sum.
	 *
	 * Currently, looks for only '.txt' file references but this can be expanded to any other file types if necessary.
	 *
	 * @param $sum
	 * @param $filesReferenced
	 * @return array
	 */
	private function checkForReferences($sum, $filesReferenced):array {
		$fileContents = [];
		$filePaths = null;
		foreach ($filesReferenced as $fileReferenced) {
			if (!file_exists($fileReferenced)) {
				continue;
			}

			$fileContents[] = $this->getFileContents($fileReferenced);
		}
		foreach($fileContents as $fileContent) {
			$sumOfRefFile = array_sum($fileContent);
			$sum = $sum + $sumOfRefFile;
			$filePaths = preg_grep(self::FILETYPES, $fileContent);
		}
		return [$sum, $filePaths];
	}

	/**
	 *
	 * When called creates sample files used for testing.
	 *
	 * @return array
	 */
	public function createFiles($amountOfFiles, $amountOfNumbersPerFile):array {
		// Create sample files and return true on success.
		$amountOfFiles = $amountOfFiles - 1;
		$alphabet = range('A', 'Z');
		foreach (range('A', $alphabet[$amountOfFiles]) as $key => $value) {
			$file = fopen($value . ".txt", "w");
			$text = '';
			for ($j = 1; $j <= $amountOfNumbersPerFile; $j++) {
				$number = rand(1, 50) . "\n";
				$text = $text . $number;
			}
			if ($value !== $alphabet[$amountOfFiles]) {
				$text = $text . $alphabet[$key+1] . '.txt' . "\n";
			}
			fwrite($file, $text);
			fclose($file);
		}
		return [$amountOfFiles + 1, $amountOfNumbersPerFile, $alphabet];
	}

}

// Use argument parameter passed. If argument does not exist then create new files and choose a file as argument.
$startTime = microtime(true);
echo "________________________________________________________________________\n";
$sumFiles = new sumFiles($argv[1] ?? '');
// Call sum function.
echo 'Total sum of numbers in file(s): ' . $sumFiles->sumDescFiles() . "\n";
echo "________________________________________________________________________\n";
echo "\e[0;32mCompleted in: " . microtime(true) - $startTime . " seconds.\e[0m\n";
