<?php

/**
 * Exercice 2 : heritage, portee et boucle jusqu'a 100 000 km.
 *
 * Meme structure que td1.php : PHP pur, puis l'enveloppe HTML, puis le contenu genere dans un <pre>. Se reporter a td1.php pour le detail de cette organisation et du role de la balise <pre>.
 *
 * La difference tient en une ligne : ce fichier declare le typage strict.
 */

// Exercice 1, Q3 : a placer en PREMIERE instruction du fichier, juste apres <?php. Sans cette ligne, PHP convertit silencieusement les arguments, et surface("12", "7.9") rend 84 au lieu de 94,8. Avec elle, il refuse.
//
// Le typage strict ne s'applique qu'au fichier ou il est declare : il n'est pas herite par les fichiers inclus. Chaque fichier doit donc porter la sienne, y compris ceux de src/.
declare(strict_types=1);

require("../src/Vehicule.php");
require("../src/VehiculeDiesel.php");
require("../src/VehiculeElectrique.php");
require("../src/Exceptions/DistanceException.php");
require("../src/Exceptions/ReservoirVideException.php");
require("../src/Exceptions/PaiementRefuseException.php");

// Exercice 2, Q1 : les trois vehicules du tableau de l'enonce.
//
// Les trois se construisent de la meme facon, avec la meme signature : c'est toute la promesse de l'heritage. Seule change la classe instanciee, et avec elle l'autonomie et le carburant accepte, declares dans les filles.
//
// Noter qu'un quatrieme argument est passe alors que le constructeur n'en attend que trois. PHP accepte les arguments surnumeraires sur une methode
// definie par l'utilisateur et les ignore : le carburant indique ici n'a donc aucun effet, il vient en realite de la constante CARBUTYPE de chaque classe. Voir la note de relecture.
$clio = new Vehicule("Renault Clio", 90, 15_000, Carburant::SansPlomb);
$yeti = new VehiculeDiesel("Skoda Yeti", 160, 20_000, Carburant::Ethanol);
$zoe  = new VehiculeElectrique("Renault Zoe", 135, 5_000, Carburant::Electricite);

// Méthode pour récupérer la distance parcourue -> getKilometrage de la classe Vehicule.
// On crée un tableau pour stocker les véhicules et leurs pleins + paiements refusés
//
// Exercice 2, Q2 : les trois vehicules dans un tableau, pour les traiter identiquement dans une seule boucle. Le code qui suit ne sait plus quelle classe il manipule, et n'a pas besoin de le savoir : c'est le polymorphisme a l'oeuvre.
$cars = [$clio, $yeti, $zoe];

$roadTo100K = [];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes voitures</title>
    <style>
        pre {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Comme dans td1.php : <pre> preserve les retours a la ligne. -->
    <pre>
<?php

/*
 * Exercice 2, Q2 : amener chaque vehicule a 100 000 km.
 *
 * L'algorithme tient en une phrase : tant que l'objectif n'est pas atteint, essayer de rouler ; si le reservoir est vide, faire le plein et recommencer.
 *
 * Deux points meritent qu'on s'y arrete.
 *
 * LES DEUX try IMBRIQUES. Le plein peut echouer lui aussi, et pour une raison differente. Un seul niveau ne suffirait pas : il faut attraper la panne seche, puis, a l'interieur, le refus de paiement. Chaque exception est traitee la ou on sait quoi en faire.
 *
 * LA ZOE. Son autonomie est de 400 km, et on lui demande des etapes de 500. La boucle se termine pourtant, parce que roule() credite les kilometres reellement parcourus avant de lever l'exception : la Zoe avance de 400 km par tour. Avec une implementation qui refuserait tout en bloc sans rien crediter, on ferait le plein indefiniment sans jamais avancer d'un metre, et le terminal resterait fige.
 *
 * Dernier detail, qui repond a la question de l'enonce sur l'ecart au kilometrage attendu : la condition est testee AVANT l'etape, pas apres. Un vehicule a 99 900 km repart donc pour une etape complete, et depasse legerement les 100 000.
 */
foreach ($cars as $car){
    $nbPleins=0;
    $nbPaiementRefuses=0;
    while ($car->getKilometrage() < 100000){
        try {
            $car->roule(500);
        } catch (Exceptions\ReservoirVideException $e) {
            // Panne seche : on passe a la station.
            try{
                // getCarbuType() rend le carburant du vehicule lui-meme : ce plein ne peut donc jamais echouer pour cause de mauvais carburant. C'est voulu ici, mais c'est aussi pourquoi MauvaisCarburantException ne se declenchera jamais dans ce fichier : pour la tester, il faut passer un carburant qui ne vient pas du vehicule.
                $car->faitLePlein($car->getCarbuType());
                $nbPleins +=1;
            } catch (Exceptions\PaiementRefuseException $e) {
                // Le refus n'interrompt pas la boucle : le vehicule n'a pas avance, mais il n'a rien perdu non plus. On compte, et le tour suivant retentera un plein.
                echo PHP_EOL . "Paiement refusé ! " . $e . PHP_EOL;
                $nbPaiementRefuses +=1;
        }
        }
    }
    echo "Trajet à 100.000 km... fait !<br>";
    $roadTo100K[] = [$car, $nbPleins, $nbPaiementRefuses];
    echo "La voiture ".$car->getMarque()." a fait ".$nbPleins." pleins et a essuyé ".$nbPaiementRefuses." paiements refusés." . PHP_EOL;
}



echo "// affiche les caractéristiques des véhicules" . PHP_EOL;

// Comparez les trois lignes produites, c'est le resume de l'exercice 2 : meme code, memes appels, trois comportements differents, uniquement parce que chaque classe fille declare ses propres constantes. La Zoe aura fait pres de trois fois plus de pleins que le Yeti pour la meme distance.
echo $clio->getCaracteristiques() . PHP_EOL;
echo $yeti->getCaracteristiques() . PHP_EOL;
echo $zoe->getCaracteristiques() . PHP_EOL;

?>
    </pre>
</body>
</html>
