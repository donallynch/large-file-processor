# large-file-processor
Laravel application that efficiently reads and processes (possibly) very large csv files. One line of file is read per cycle to avoid memory overflows and the max memory footprint therefore depends on the longest line in the input file.
