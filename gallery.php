<?php

$lines = file_get_contents("zs_fot.html");
$lines = explode("\n", $lines);

foreach($lines as $line)
{
    //$line = iconv("cp1250", "utf-8", $line);
	if(strpos($line, "CENTER"))
	{
		echo "<br/><br/>" . strip_tags($line) . "<br/>";
	}else {
		$name = preg_match("*(f_[a-zA-z]+[0-9]+.jpg)*", $line, $matches);
		echo $matches[0] . "<br />";
	}
}

?>