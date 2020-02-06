<?php

namespace App\Services;

/**
 * Class LineByLineFileReader
 *
 *  DRIVER: http://localhost/?url=DATA_VALID_URL
 *
 * @package App\Services
 */
class LineByLineFileReader
{
    private $requestedFile;
    private $inputFilePath;
    private $earliestDate;
    private $compressionPath = 'compress.zlib://';
    private $badDate = '1970-01-01 00:00:00';
    private $earliestUser = -1;
    private $spend = 0;
    private $count = 0;

    const INITIAL_ROW = 'user_id';
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
    const DATE_FORMAT = 'Y-m-d H:i:s';
    const READ_ONLY = "r";
    const COMMA = ',';

    /**
     * LineByLineFileReader constructor.
     */
    public function __construct()
    {
        $this->requestedFile = config('app.REQUESTED_FILE');
        $this->inputFilePath = config('app.INPUT_FILE_PATH');
        $this->earliestDate = date(self::DATE_FORMAT);
    }

    /**
     * Determine number of users with the specified resolution
     *
     * @param string $url
     * @return array
     */
    public function process(string $url)
    {
        $this->acquireFile($url);

        /**
         * Read massive file line by line
         *  The maximum memory (RAM) needed depends on the longest line in the input file
         */
        $handle = fopen($this->inputFilePath, self::READ_ONLY);

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
            $line = explode(self::COMMA, $line);

            /* Basic line validation */
            if (!is_array($line) || count($line)!== self::LINE_ITEM_COUNT) {
                exit('400-line-validation-failed');
            }

            /* Skip initial row with column names */
            if ($line[self::USER_ID] === self::INITIAL_ROW) {
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
            $date = date(self::DATE_FORMAT, strtotime($line[self::DATE_JOINED]));
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
            if ($line[self::DATE_JOINED] < $this->earliestDate) {
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
    private function acquireFile(string $url)
    {
        /**
         * Try to acquire input file
         *  File may not exist or may be corrupt
         *  Throw exception if there's an issue
         */
        try {
            /* Download and decompress requested (gzipped/compressed) file */
            $file = file_get_contents($this->compressionPath . $url);

            /* If file exists already */
            if (file_exists($this->inputFilePath)) {
                unlink($this->inputFilePath);
            }

            /* Touch and put contents */
            touch($this->inputFilePath);
            file_put_contents($this->inputFilePath, $file);
        } catch (\Exception $e) {
            exit('400-error-while-downloading-input-file');
        }

        return true;
    }
}

