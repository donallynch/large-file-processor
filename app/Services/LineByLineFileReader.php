<?php

namespace App\Services;

/**
 * Class LineByLineFileReader
 * @package App\Services
 */
class LineByLineFileReader
{
    private $requestedFile = "https://s3.amazonaws.com/swrve-public/full_stack_programming_test/test_data.csv.gz";
    private $compressionPath = 'compress.zlib://';
    private $inputFilePath = 'data/data.csv';
    private $badDate = '1970-01-01 00:00:00';
    private $earliestDate;
    private $earliestUser = -1;
    private $spend = '0';
    private $count = 0;

    const BAD_REQUEST = 400;
    const MD5_LENGTH = 32;
    const TARGET_WIDTH = 640;
    const TARGET_HEIGHT = 960;
    const USER_ID = 0;
    const DATE_JOINED = 1;
    const SPEND = 2;
    const MILLISECONDS_PLAYED = 3;
    const DEVICE_HEIGHT = 4;
    const DEVICE_WIDTH = 5;
    const LINE_ITEM_COUNT = 6;

    /**
     * LineByLineFileReader constructor.
     */
    public function __construct()
    {
        $this->earliestDate = date('Y-m-d H:i:s');
    }

    /**
     * Determine number of users with a device resolution of 640x960 (width x height)
     *
     * @return array
     */
    public function process()
    {
        $this->acquireFile();

        /**
         * Read massive file line by line
         *  The maximum memory (RAM) needed depends on the longest line in the input file
         */
        $handle = fopen("data/data.csv", "r");

        /* If there's a problem location or accessing file */
        if (!$handle) {
            exit(self::BAD_REQUEST);
        }

        /**
         * Read single line of file (to avoid loading massive file into RAM/Memory)
         */
        $i = 0;
        while (($line = fgets($handle)) !== false) {

            /* Convert single line to array */
            $line = explode(',', $line);

            /* Basic line validation */
            if (!is_array($line) || count($line)!== self::LINE_ITEM_COUNT) {
                exit('400-line-validation-failed');
            }

            /* Skip initial row with column names */
            if ($line[0] === 'user_id') {
                continue;
            }

            /* Convert numeric items (so we can use them in calculations later) */
            $line[2] = floatval($line[self::SPEND]);
            $line[3] = floatval($line[self::MILLISECONDS_PLAYED]);
            $line[4] = floatval($line[self::DEVICE_HEIGHT]);
            $line[5] = floatval($line[self::DEVICE_WIDTH]);

            /* Validate line */
            if (!is_string($line[self::USER_ID])
                || strlen($line[self::USER_ID]) !== self::MD5_LENGTH
                || !is_numeric($line[self::SPEND])
                || !is_numeric($line[self::MILLISECONDS_PLAYED])
                || !is_numeric($line[self::DEVICE_HEIGHT])
                || !is_numeric($line[self::DEVICE_WIDTH])
            ) {
                exit('400-validation-failed');
            }
            /* Validate date */
            $date = date('Y-m-d H:i:s', strtotime($line[self::DATE_JOINED]));
            if ($date === $this->badDate) {
                exit('400-date-validation-failed');
            }

            /* Add each spend value */
            $this->spend += $line[self::SPEND];

            /* Count users with large screens */
            if ($line[self::DEVICE_WIDTH] > self::TARGET_WIDTH && $line[self::DEVICE_HEIGHT] > self::TARGET_HEIGHT) {
                $this->count++;
            }

            /* Calculate earliest date */
            if ($line[1] < $this->earliestDate) {
                $this->earliestDate = $line[self::DATE_JOINED];
                $this->earliestUser = $line[self::USER_ID];
            }

            $i++;
        }

        /* Close file handler */
        fclose($handle);

        return [
            'totalCountOfUsers' => $i,
            'countLargeScreenUsers' => $this->count,
            'totalSpendDollars' => $this->spend,
            'earliestSignupDate' => $this->earliestDate,
            'earliestUserId' => $this->earliestUser,
        ];
    }

    /**
     * @return bool
     */
    private function acquireFile()
    {
        /* Download and decompress requested (gzipped/compressed) file */
        $file = file_get_contents($this->compressionPath . $this->requestedFile);

        /* If file exists already */
        if (file_exists($this->inputFilePath)) {
            unlink($this->inputFilePath);
        }

        /* Touch and put contents */
        touch($this->inputFilePath);
        file_put_contents($this->inputFilePath, $file);

        return true;
    }
}

