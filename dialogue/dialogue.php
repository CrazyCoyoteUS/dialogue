<?php

// EXERCICES : espace de dialogue
//-------------------------------
// 01 - Création de la BDD
// bdd = dialogue
// table = commentaire
// id_commentaire INT(3) PK AI
// pseudo VARCHAR (200)
// message TEXT
// date_enregistrement DATETIME

// 02 - Créer une connexion à la BDD
$host = 'mysql:host=localhost;dbname=dialogue';
$login = 'root';
$password = 'root';
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

$pdo = new PDO($host, $login, $password, $options);

// 03 - Faire un formulaire avec les champs suivants :
// pseudo input type="text"
// message textarea
// bouton de validation

// 04 - Récupération des données du form + contrôles
// 05 - Déclenchement d'une requete permettant d'enregistrer le message dans la BDD
// 06 - Déclenchement d'une requete de récupération pour récupérer tous les messages en BDD
// 07 - Affichage des commentaires dans la page du plus récent vers le plus ancien
// 08 - Affichage en haut des messages du nombre de messages
// 09 - Affichage de la date_enregistrement en fr
// 10 - Amélioration de la mise en forme




$msg = '';
$req = '';
if (isset($_POST['pseudo']) && isset($_POST['message'])) {
    $pseudo = trim($_POST['pseudo']);
    $message = trim($_POST['message']);

    // $message = addslashes($message); // addslashes rajoute un anti slash \ devant les ' et les " de la chaine

    $erreur = false;

    if (empty($pseudo)) {
        $msg .= '<div class="alert alert-danger mb-3">Le pseudo est obligatoire !</div>';
        $erreur = true;
    }

    if (iconv_strlen($message) < 1) {
        $msg .= '<div class="alert alert-danger mb-3">Le message est obligatoire !</div>';
        $erreur = true;
    }

    if ($erreur == false) {
        // enregistrement
        // $req = "INSERT INTO commentaire (id_commentaire, pseudo, message, date_enregistrement) VALUES (NULL, '$pseudo', '$message', NOW() )";
        // $pdo->query($req);

        // Pour éviter les injections SQL : prepare
        $enregistrement = $pdo->prepare("INSERT INTO commentaire (id_commentaire, pseudo, message, date_enregistrement) VALUES (NULL, :pseudo, :message, NOW() )");
        $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(':message', $message, PDO::PARAM_STR);
        $enregistrement->execute();
    }
}

// récupération des messages
$liste_messages = $pdo->query("SELECT pseudo, message, DATE_FORMAT(date_enregistrement, '%d/%m/%Y %H:%i:%s') AS date_fr FROM commentaire ORDER BY date_enregistrement DESC");

?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace de dialogue</title>

    <!-- Bootstrap : CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- CSS projet -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@1,700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="p-5 bg-success text-white text-center">
        <h1 class="p-5 pb-3 border-bottom">DIALOGUE ⌨</h1>
    </header>
    <div class="container border">
        <div class="row mt-5">
            <div class="col-sm-6 mx-auto">
                <?php echo $msg;
                echo '<hr>';
                echo $req; ?>
                <form method="post" class="border p-3">
                    <div class="mb-3">
                        <label for="pseudo" class="form-label">Pseudo</label>
                        <input type="text" name="pseudo" class="form-control" id="pseudo">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" class="form-control" id="message"></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-success w-100">📃 Valider 📃</button>
                </form>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-sm-6 mx-auto">
                <h2 class="text-center pb-3 border-bottom">Messages :</h2>
                <?php

                echo '<p>Il y a ' . $liste_messages->rowCount() . '  messages</p>';

                while ($com = $liste_messages->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="card mb-3">
                        <h5 class="card-header">Par : <?php echo htmlspecialchars($com['pseudo']); ?>, le <?php echo $com['date_fr'] ?></h5>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($com['message']); ?></p>
                        </div>
                    </div>

                <?php
                }

                ?>
            </div>
        </div>
    </div>



    <!-- Bootstrap : JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <!-- JS projet -->
    <script src="js/script.js"></script>
</body>

</html>