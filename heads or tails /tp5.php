<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Pile ou Face</title>
    </head>

    <body>
        <?php
            $host = 'localhost:8889';
            $user = 'root';
            $pass = 'root';
            $nom = 'TP5';
            $conn = mysqli_connect($host, $user, $pass, $nom);

            if(! $conn ){
                die('Could not connect: ' . mysqli_error($conn));
            }

            if(isset($_POST['pseudo_1'])){
                $joueur_1 = $_POST['pseudo_1'];
            }else{
                $joueur_1 = "pseudo_1";
            }

            if(isset($_POST['pseudo_2'])){
                $joueur_2 = $_POST['pseudo_2'];
            }else{
                $joueur_2 = "pseudo_2";
            }

            if(isset($_POST['message_1'])){
                $choix_1 = $_POST['message_1'];
            }else{
                $choix_1 = "pile";
            }

            if(isset($_POST['message_2'])){
                $choix_2 = $_POST['message_2'];
            }else{
                $choix_2 = "face";
            }

            if(isset($_POST['echange'])){
                $echange = $choix_1;
                $choix_1 = $choix_2;
                $choix_2 = $echange;
            }

            if(isset($_POST['sanctionner'])){
                if(isset($_POST['sanction_joueur'])){
                    sanctionApply($conn,$_POST['sanction_joueur']);
                }else{
                    echo 'Erreur : entrez un pseudo';
                }
            }

            if(isset($_POST['bannir'])){
                if(isset($_POST['bannir_joueur'])){
                    banishApply($conn,$_POST['bannir_joueur']);
                }else{
                    echo 'Erreur : entrez un pseudo';
                }
            }
        ?>

        <form action="tp5.php" method="post" >

            <input name="message_1" value="<?php echo $choix_1;?>" type="hidden">
            <input name="message_2" value="<?php echo $choix_2;?>" type="hidden">

            <p>
                Joueur 1 : <input type="text" name="pseudo_1" placeholder="pseudo_1" value="<?php echo $joueur_1; ?>"/>
                Joueur 2 : <input type="text" name="pseudo_2" placeholder="pseudo_2" value="<?php echo $joueur_2; ?>" />
            </p>
            <p>
                <?php
                    echo 'le joueur 1 a choisi '. $choix_1. ' et le joueur 2 a choisi '. $choix_2 . "\n";
                ?>
                <input type="submit" value="Changer la prédiction" name="echange">
            </p>

            <input type="submit" value="Lancer la pièce !" name="lancer">

            <p>
                <?php
                    if(isset($_POST['lancer'])){
                        $nbr = rand(0,1);
                        $msg = "";
                        if($nbr){
                            echo 'la piece est tombée sur le côté pile.';
                            $msg = "pile";
                        }else{
                            echo 'la piece est tombée sur le coté face.';
                            $msg = "face";
                        }

                        if($choix_1 == $msg ){
                            echo 'Le joueur '. $joueur_1 .' a gagné !';
                            checkPlayers($conn, $joueur_1, 1);
                            checkPlayers($conn, $joueur_2, 0);
                        }else {
                            echo 'le joueur ' . $joueur_2 . ' a gagné !';
                            checkPlayers($conn, $joueur_1, 0);
                            checkPlayers($conn, $joueur_2, 1);
                        }
                    }
                ?>
            </p>
        </form>

        <h2> Classement mondial </h2>
        <table>
            <thead>
                <tr>
                    <th> Pseudos </th>
                    <th> Nombre de match </th>
                    <th> Victoires </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nbr_joueur = 0;
                    $nbr_match = 0;
                    $var = "SELECT * FROM Joueurs ORDER BY victoire DESC";
                    $res = mysqli_query($conn,$var);

                    if(mysqli_num_rows($res)>0){
                        while($val = mysqli_fetch_row($res)){
                            echo '<tr>
                                <td>'. $val[1].'</td>
                                <td>'. $val[2].'</td>
                                <td>'. $val[3].'</td>
                            </tr>';
                            $nbr_joueur++;
                            $nbr_match+=$val[2];
                        }
                    }
                ?>
            </tbody>
        </table>

        <h2> Statistiques </h2>
        <p>
            <?php
            echo nl2br("Nombre de joueurs : ".$nbr_joueur."\n");
            echo "Nombre de matchs joués : ".$nbr_match/2;
            ?>
        </p>

        <h2> Sanctions et Banissements </h2>
        <form action="tp5.php" method="post">
            <input type="text" name="sanction_joueur" placeholder="Pseudo du joueur à sanctionner ">
            <input type="submit" name="sanctionner" value="Sanctionner">
        </form>

        <form action="tp5.php" method="post">
            <input type="text" name="bannir_joueur" placeholder="Pseudo du joueur à bannir">
            <input type="submit" name="bannir" value="Bannir">
        </form>

    </body>
    <?php mysqli_close($conn); ?>
</html>

<?php

function checkPlayers($conn, $joueur, $gagnant){
    $var = "SELECT pseudo FROM Joueurs";
    $resultat = $conn->query($var);
    $bool=TRUE;
    if($resultat->nbr_lignes>0){
        while($row = $resultat->fetch_assoc()){
            if($row['pseudo']===$joueur){
                updateInfo($conn,$joueur,$gagnant);
                $bool=FALSE;
            }
        }
    }
    if($bool){
        addPlayers($conn, $joueur, $gagnant);
    }
}

function addPlayers($conn, $joueur, $gagnant){
    if($gagnant){
        mysqli_query($conn, "INSERT INTO Joueurs (id, pseudo, nbr_match, victoire) VALUES (NULL,'".$joueur."', 1, 1)");
    }else{
        mysqli_query($conn, "INSERT INTO Joueurs (id, pseudo, nbr_match, victoire) VALUES (NULL, '".$joueur."', 1, 0)");
    }
}

function updateInfo($conn, $joueur, $gagnant){
    $var = "UPDATE Joueurs SET nbr_match=nbr_match+1, victoire=victoire+".$gagnant." WHERE pseudo='".$joueur."'";
    mysqli_query($conn,$var);
}

function sanctionApply($conn, $joueur){
    $var = "UPDATE Joueurs SET victoire=0 WHERE pseudo='".$joueur."'";
    try{
        mysqli_query($conn,$var);
    }catch (Exception $exc){}
}

function banishApply($conn,$joueur){
    try{
        $var = "DELETE FROM Joueurs WHERE pseudo='".$joueur."'";
        mysqli_query($conn,$var);
    }catch (Exception $exc){}
}
?>

