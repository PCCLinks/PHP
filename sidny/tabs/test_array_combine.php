<?php

$a1=array("a","b","c","d");
$a2=array("Cat","Dog","Horse","Cow");
print_r(array_combine($a1,$a2));

$c = array_combine($a1,$a2);

echo "c1 = $c[a]";

$c0 = $c[$a1[0]];

echo $c0;

?>
