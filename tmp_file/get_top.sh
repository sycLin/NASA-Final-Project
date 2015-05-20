#!/bin/bash

# This shell script is used on linux15@csie
# To retreive sample process information
# For Brian to build html templates

data="`top -n 1 -b`"

echo "$data" > sample_tmp

o_linecount=`wc -l sample_tmp | cut -d ' ' -f 1`

echo "original line count = $o_linecount"

n_linecount=$(($o_linecount - 6))

tail -$n_linecount sample_tmp > sample_proc_info

# clean up
rm -f sample_tmp
