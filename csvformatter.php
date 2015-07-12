#!/usr/bin/php
<?php

// error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/lib/csv/Dialect.php');
require_once(dirname(__FILE__) . '/lib/csv/Reader/Abstract.php');
require_once(dirname(__FILE__) . '/lib/csv/Reader.php');

/*********************************************************************/
function print_usage_and_exit($error = false)
{
	if ($error)	
	{
		$error .= "\n";
	}
	else 
	{
		$error = '';
	}
	
	global $argv;
	$usage = sprintf("Usage: %s <filter-script> <input-file> <output-file> [mode] [delimiter]\n",
		$argv[0]) .
"Copyright 2010 Gurock Software GmbH. All rights reserved.

<filter-script> a PHP script to extract the CSV data for conversion.
See the project website for more details.

<input-file> should be the filename of a CSV file with test cases
you want to convert (for example, an exported Excel file).

<output-file> specifies the filename of the resulting TestRail
import/export file.

[mode] An optional mode. The following modes are available:

  --export  The default behavior; exports the data to the XML file.
  --csv     For debugging: prints the CSV data as seen by the script
  --cases   For debugging: prints the cases after the filter script
            was called
  --tree    For debugging: prints the section/case tree after analyzing
            the cases and sections
			
[delimiter] Allows you to override the default comma delimiter.";

	print_error_and_exit($error . $usage);
}

/*********************************************************************/
function read_input($filename, $delimiter)
{
	$dialect = new Csv_Dialect();
	$dialect->delimiter = $delimiter;
	try
	{
		$reader = new Csv_Reader($filename, $dialect);
	}
	catch (Exception $e)
	{
		print_error_and_exit('Could not open the specified input file');
	}
	$rows = $reader->toArray();
	return $rows;
}

/*********************************************************************/
function formatter($csv, $conf) {
	$res = array();
	foreach($csv as $key => $row) {
		if ($key < $conf["start_at"]) {
			$res[] = $row;
			continue;
		}
		$split_rows = array();
		$max_row_index = $conf["rows"][0];
		$min_row_index = $conf["rows"][1];
		foreach($conf["rows"] as $index) {
			$split_rows[$index] = array();
			foreach (explode("\n", $row[$index]) as $k => $v) {
				array_push($split_rows[$index], array(
					trim(str_replace(trim(preg_replace($conf["prefix"], "", $v)), "", $v)),
					preg_replace("/^(\-|\ |\- |\ \-)/", "", (preg_replace($conf["prefix"], "", $v)))
				));
			}
		}
		// var_dump(array($max_row_index, $min_row_index, $split_rows));
		// exit();
		foreach ($split_rows[$max_row_index] as $key => $value) {
			$new_row = $key == 0 ? $row : array_fill(0, count($row), "");
			$new_row[$conf["id"]] = $row[$conf["id"]];
			$new_row[$max_row_index] = $value[1];
			$new_row[$min_row_index] = "";
			if ($value[0] == "") {
				$new_row[$min_row_index] = "";
			} else {
				foreach ($split_rows[$min_row_index] as $minKey => $minValue) {
					if ($minValue[0] == $value[0] || ($key == (count($split_rows[$max_row_index]) - 1) && $minValue[0] == "")) {
						$new_row[$min_row_index] = $new_row[$min_row_index]." ".$minValue[1];
					}
				}
			}
			$new_row[$min_row_index] = trim($new_row[$min_row_index]);
			// var_dump($new_row);
			// exit();
			$res[] = $new_row;
		}
	}
	// var_dump($res);
	return $res;
}

function write_output($handle, $csv) {
	foreach ($csv as $row) {
		write_row($handle, $row);
	}
}

function write_row($handle, $row) {
	foreach ($row as $key => $value) {
		$row[$key] = preg_replace("/\"/", "\"\"", $value);
	}
	fprintf($handle, "\"%s\"\n", implode("\",\"", $row));
}

/*********************************************************************/

$filter = $argv[1];
$in = $argv[2];
$out = $argv[3];

// Process the input file
if (!file_exists($in))
{
	print_usage_and_exit("File $in not found");
}
$csv = read_input($in, isset($argv[5]) ? $argv[5] : ',');

// Filter the CSV data for conversion
if (!@include($filter))
{
	print_usage_and_exit("Could not include filter script $in");
}

$conf = formatter_filter($csv);
$conf["start_at"] = isset($conf["start_at"]) ? $conf["start_at"] : 1;
$conf["id"] = isset($conf["id"]) ? $conf["id"] : 0;
$conf["rows"] = isset($conf["rows"]) ? $conf['rows'] : array();

$csv = formatter($csv, $conf);

// Write the output
$handle = fopen($out, 'w');
if (!$handle)
{
	// print_error_and_exit('Could not create output file');
}
write_output($handle, $csv);
fclose($handle);

print("Successfully format all test cases\n");

?>
