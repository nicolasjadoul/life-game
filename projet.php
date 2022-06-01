<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="projet.css"/>
<?php
session_start();
?>
<head>
    <meta charset="utf-8">
    <title>jeu de la vie</title>
</head>

<body>

<?php




if (isset($_POST['longueur'])) {
    $longueur = intval($_POST['longueur']);
} else {
    $longueur = 17;
}
if (isset($_POST['largeur'])) {
    $largeur = intval($_POST['largeur']);
} else{
    $largeur = 17;
}
if (isset($_POST['créer'])){
    $_SESSION["compteur"] = 0;
    $_SESSION['array']=array_fill(0, $longueur, array_fill(0, $largeur, '0'));

}
if (isset($_POST[">"])) {
    update_array();
    $_SESSION["compteur"]++;
}
if (!isset($_SESSION["compteur"])) {
    $_SESSION["compteur"] = 0;

}
if (isset($_POST['up'])){
    parcourt();
}
if (isset($_POST['auto'])){

    for($i=0;$i<100;$i++){
        $_SESSION['compteur']++;
        update_array();
    }
}
if (isset($_POST["<"])){
    $_SESSION['compteur']--;

}
if (isset($_POST['random'])){
    random($longueur,$largeur);
    $_SESSION["compteur"] = 0;
}
?>


<form method="post" enctype="multipart/form-data">


    <p>
        <input type="text" name="longueur" placeholder="longueur de la grille"
               value="<?php if (isset($_POST['longueur'])) {
                   echo $_POST['longueur'];
               } ?>"/>
        <input type="text" name="largeur" placeholder="largeur de la grille"
               value="<?php if (isset($_POST['largeur'])) {
                   echo $_POST['largeur'];
               } ?>"/>
        <input type="submit" value="créer la grille" name="créer">
        <input type="submit" value="<" name="<">
        <input type="submit" value=">" name=">">
        <input type="submit" value="auto" name="auto">
        <input type="submit" value="arret" name="arret">
        <input type="submit" value="aléatoire" name="random">
    </p>
    <p>
        <?php echo 'taille de la grille : ' . $longueur . '*' . $largeur . '' ;
        echo 'nombre de génération : ' . $_SESSION["compteur"] . ''; ?>
    </p>
    <input type="file" accept=".pattern"  name="files" >
    <input type="submit" name="up">
</form>



<table>
    <?php
    foreach ($_SESSION['array'] as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            if ($cell == '0') {
                echo '<td class="white-cell"></td>';
            } else {
                echo '<td class="black-cell"></td>';
            }
        }
        echo '</tr>';
    }
    ?>
</table>


<?php

function update_array()
{
    $longueur=count($_SESSION['array']);
    $largeur=count($_SESSION['array'][0]);

    $new_array = [];

    for ($i=0; $i<$longueur;$i++) {
        for ($j = 0; $j < $largeur; $j++) {
            $neighbours = count_neighbours($_SESSION['array'],$i,$j);

            // Si cellule morte et 3 vivantes autour, alors cellule nait
            if ($_SESSION['array'][$i][$j] == '0' && $neighbours == 3) {
                $new_array[$i][$j]='1';

            }
            // Si cellule vivante et !2/3 vivantes autour, alors meurt
            else if ($_SESSION['array'][$i][$j] == '1' && ($neighbours == 1 || $neighbours > 3)) {
                $new_array[$i][$j]='0';

            } else {
                $new_array[$i][$j] = $_SESSION['array'][$i][$j];

            }

        }
    }
    $_SESSION['array'] = $new_array;

}

function count_neighbours($array, $i, $j)
{
    $count = 0;
    if (isset($array[$i - 1][$j - 1])) {
        if ($array[$i - 1][$j - 1] == '1') {
            $count++;
        }
    }
    if (isset($array[$i - 1][$j])) {
        if ($array[$i - 1][$j] == '1') {
            $count++;
        }
    }
    if (isset($array[$i - 1][$j + 1])) {
        if ($array[$i - 1][$j + 1] == '1') {
            $count++;
        }
    }
    if (isset($array[$i][$j - 1])) {
        if ($array[$i][$j - 1] == '1') {
            $count++;
        }
    }
    if (isset($array[$i][$j + 1])) {
        if ($array[$i][$j + 1] == '1') {
            $count++;
        }
    }
    if (isset($array[$i + 1][$j - 1])) {
        if ($array[$i + 1][$j - 1] == '1') {
            $count++;
        }
    }
    if (isset($array[$i + 1][$j])) {
        if ($array[$i + 1][$j] == '1') {
            $count++;
        }
    }
    if (isset($array[$i + 1][$j + 1])) {
        if ($array[$i + 1][$j + 1] == '1') {
            $count++;
        }
    }

    return $count;
}
function random($n,$m){
    for($i=0;$i<$n;$i++){
        for($j=0;$j<$m;$j++){
            $_SESSION['array'][$i][$j]=mt_rand(0,1);
        }
    }
}

function parcourt(){
    $txt_files=file_get_contents($_FILES['files']['tmp_name']);
    $rows=explode("\r\n",$txt_files);
    array_shift($rows);
    for ($i=0;$i<count($rows);$i++) {
        $rows[$i]=explode(',',$rows[$i]);
    }
    $_SESSION['array']=$rows;
}


?>


<select name="config">
    <option value="">charger</option>
    <option name="beacon">beacon</option>
    <option name="blinker">blinker</option>
    <option name="glider">glider</option>
    <option name="pulsar">pulsar</option>
</select>
<input type="submit" name="save" value="sauvegarder">



</body>
</html>


