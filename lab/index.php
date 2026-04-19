<?php
$length = 10;
$width = 5;
$area = $length * $width;

$perimeter = 2 * ($length + $width);

echo "Area:". $area . "<br>";
echo "Perimeter: " . $perimeter;
?>
<br>

<?php
$amount = 1000;
$vat = $amount * 0.15;

echo "Amount: " . $amount . "<br>";
echo "VAT: " . $vat;
?>
<br>

<?php
$number = 7;

if ($number % 2 == 0) {
    echo "The number". $number . " is Even";
} else {
    echo "The number". $number . " is Odd";
}
?>
<br>

<?php
$a = 25;
$b = 35;
$c = 15;

if ($a >= $b && $a >= $c){
    echo "Largest number is: " . $a;
}
elseif ($b >= $a && $b >= $c) {
    echo "Largest number is: " . $b;
}
else{
    echo "Largest number is: " . $c;
}
?>
<br>

<?php
for ($i = 10; $i <= 100; $i++) {
    if ($i % 2 != 0) {
        echo $i . " ";
    }
}
?>
<br>

<?php
$array = array(5, 10, 15, 20, 25);
$search = 15;
$found = false;

foreach ($array as $value) {
    if ($value == $search) {
        $found = true;
        break;
    }
}

if ($found) {
    echo "Element found ";
}
else {
    echo "Element not found";
}
?>
<br>

<?php
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo "* ";
    }
    echo "<br>";
}

for ($i = 3; $i >= 1; $i--) {
    for ($j = 1; $j <= $i; $j++) {
        echo $j . " ";
    }
    echo "<br>";
}

$char = 'A';

for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo $char . " ";
        $char++;
    }
    echo "<br>";
}

?>
