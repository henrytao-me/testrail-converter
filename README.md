# testrail-converter

CSV Migration Script
--------------------

This script can be used to convert test cases stored in CSV and Excel 
to TestRail's XML import file format.

You can use the script as follows:

> php csv2testrail.php <filter-script> <input-file> <output-file> [mode] [delimiter]

> php csvformatter.php <filter-script> <input-file> <output-file>

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
			
[delimiter] Allows you to override the default comma delimiter.

You can learn more about this migration script on its project website:
http://code.gurock.com/p/testrail-migrate/


Included third-party code:
--------------------------

PHP CSV Utilities (c) 2010 Luke Visinoni
Published under the GNU Lesser General Public License:
http://code.google.com/p/php-csv-utils/

-- 
Copyright 2010 Gurock Software GmbH. All rights reserved.
http://www.gurock.com - contact@gurock.com

http://stackoverflow.com/questions/2507608/error-input-is-not-proper-utf-8-indicate-encoding-using-phps-simplexml-lo

