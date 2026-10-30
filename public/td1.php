<?php

/**
 * Exercice 1 : l'application.
 *
 * Ce fichier est le SEUL de l'exercice a contenir des echo. Les classes de src/ portent les donnees et le comportement ; c'est ici, et ici seulement, qu'on decide de ce qui s'affiche et comment.
 *
 * Il est organise en trois parties, et cet ordre a un sens :
 *
 *   1. du PHP pur       : on charge les classes, on cree les objets
 *   2. ?> puis du HTML  : l'enveloppe de la page
 *   3. <?php a nouveau  : le contenu, genere a l'interieur du <pre>
 *
 * La partie 1 doit rester AVANT toute sortie. C'est sans consequence ici, mais ce sera une regle absolue des le TD7 : des qu'on envoie un en-tete HTTP ou qu'on fait une redirection, plus rien ne doit avoir ete affiche auparavant.
 */
// Chargement des classes. Sans autoloader, chaque fichier doit etre inclus a la main, et l'oubli d'un require se traduit par « Class ... not found ».
//
// Les chemins sont relatifs : ils sont resolus depuis le DOSSIER DE TRAVAIL COURANT, pas depuis l'emplacement de ce fichier. Ils fonctionnent donc si l'on travaille depuis public/, ou via php -S -t public/, mais pas si l'on lance « php public/td1.php » depuis la racine du projet.
require("../src/Vehicule.php");
require("../src/Exceptions/DistanceException.php");
require("../src/Exceptions/ReservoirVideException.php");
require("../src/Exceptions/PaiementRefuseException.php");

// Exercice 1, Q1 : les deux vehicules du tableau de l'enonce.
// 15_000 et 15000 sont le meme nombre ; le tiret du 8 est un simple separateur // de milliers, ignore par PHP.
$vehicule1 = new Vehicule("Renault", 90, 15_000);
$vehicule2 = new Vehicule("Peugeot", 110, 20_000);

/*
*  La balise de fermeture ci-dessous quitte le mode PHP. Tout ce qui suit est
*  envoye tel quel au navigateur, jusqu'a la prochaine balise d'ouverture.
*  Noter qu'il n'y a aucun echo pour le HTML : c'est bien plus lisible que de
*  l'empiler dans des chaines de caracteres.
*
*  Au passage, un piege : la sequence ?> placée dans un commentaire // quitte elle aussi le mode PHP, et la fin du commentaire se retrouve affichee dans la page. Dans un commentaire de bloc comme celui-ci, elle est sans effet.
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
      charset="UTF-8" n'est pas decoratif : sans cette ligne, le navigateur devine l'encodage, et les accents de « Kilométrage » comme les emojis des messages d'erreur s'affichent en charabia. C'est la cause numero un des « é » transformes en « Ã© ».

      viewport sert a l'affichage sur telephone : sans elle, le navigateur mobile simule un ecran large et reduit tout.
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes voitures</title>
    <style>
        /*
          Un peu de CSS, uniquement pour le confort de lecture : la sortie est destinee a etre projetee ou lue a l'ecran pendant le TD.
        */
        pre {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!--
      LA BALISE LA PLUS IMPORTANTE DE CETTE PAGE.

      HTML ecrase les espaces, les tabulations et les retours a la ligne : une suite de blancs, quelle qu'elle soit, s'affiche comme un espace unique.
      C'est pour cela qu'un programme ecrit pour le terminal, truffe de \n, devient une seule longue ligne illisible dans le navigateur.

      <pre> (pour « preformatted ») demande au navigateur de respecter le texte tel qu'il est ecrit. Les PHP_EOL ci-dessous produisent donc de vrais retours a la ligne, sans qu'il faille ecrire un seul <br>.

      C'est la reponse a la question de la Q1 : « que deviennent les \n et les \t dans la page web ? » Ils sont bien envoyes — un Ctrl+U le montre — mais ils ne sont pas AFFICHES, sauf a l'interieur d'un <pre>.
    -->
    <pre>
<?php

// PHP_EOL plutot que "\n" : la constante vaut le retour a la ligne du systeme sur lequel tourne le script. C'est l'ecriture portable.
echo "// affiche les caractéristiques des deux véhicules" . PHP_EOL;

// Exercice 1, Q1 puis Q2 : l'application demande une description au vehicule et l'affiche. getCaracteristiques() renvoie une chaine, elle n'affiche rien elle-meme : c'est ici que se decide la presentation.
echo $vehicule1->getCaracteristiques() . PHP_EOL;
echo $vehicule2->getCaracteristiques() . PHP_EOL;

echo PHP_EOL . "// modifie la puissance de la vehicule1 : nouvelle valeur 110 CV" . PHP_EOL;

// $vehicule1->setPuissance(110);
echo $vehicule1->getCaracteristiques() . PHP_EOL;

echo PHP_EOL . "// affiche le kilométrage des véhicules après un déplacement : le vehicule1 parcoure 3500 km et la vehicule2 parcoure 1500 km." . PHP_EOL;

// Exercice 1, Q1 : la version naive. On avance le compteur, point.
// Ni reservoir, ni refus possible : parcours() ne connait que le kilometrage.
$vehicule1->parcours(3_500);
$vehicule2->parcours(1_500);

echo $vehicule1->getCaracteristiques() . PHP_EOL;
echo $vehicule2->getCaracteristiques() . PHP_EOL;

echo PHP_EOL . "// calcul de l'autonomie de la voiture" . PHP_EOL;
echo PHP_EOL . "// vehicule1 roule 900 km" . PHP_EOL;

// Exercice 1, Q4 et Q5 : roule() remplace parcours().
//
// 900 km demandes pour 800 km d'autonomie : le vehicule roule jusqu'a la panne, credite les 800 km reellement parcourus, puis leve l'exception. Le kilometrage passe donc a 19 300 et non a 19 400.
try {
    $vehicule1->roule(900);
} catch (Exceptions\ReservoirVideException $e) {
    // Le nom est ecrit en entier, avec son namespace, parce que ce fichier n'a pas de « use Exceptions\ReservoirVideException; » en tete.
    //
    // La concatenation « . $e » declenche __toString() de l'exception, d'ou l'affichage compact « [Exceptions\ReservoirVideException]: ... » au lieu de la trace d'appel complete.
    echo PHP_EOL . "❌ Erreur ! " . $e . PHP_EOL;
} finally {
    // finally s'execute DANS TOUS LES CAS : exception ou non, attrapee ou non. C'est le bon endroit pour ce qui doit avoir lieu quoi qu'il arrive — ici, afficher l'etat du vehicule apres la tentative, et constater que l'objet est reste coherent : reservoir vide ET kilometres credites.
    echo $vehicule1->getCaracteristiques() . PHP_EOL;
}

// Exercice 1, Q5 : la station-service.
//
// « carburant::SansPlomb » s'ecrit ici sans majuscule. PHP ne distingue pas la casse des noms de classes et d'enumerations, le code fonctionne donc — mais Carburant::SansPlomb reste l'ecriture correcte.
try {
    $vehicule1->faitLePlein(carburant::SansPlomb);
    echo PHP_EOL . "La titine a fait le plein !" . PHP_EOL;
} catch (Exceptions\PaiementRefuseException $e) {
    echo PHP_EOL . "❌ Erreur ! " . $e . PHP_EOL;
}

echo PHP_EOL . "// vehicule1 roule 750 km" . PHP_EOL;
// ATTENTION : cet appel n'est pas protege par un try/catch.
// Si le plein ci-dessus a reussi, l'autonomie vaut 800 et les 750 km passent, avec seulement l'avertissement des 25 % restants. Mais une fois sur cinq le paiement est refuse : l'autonomie vaut alors 0, roule() leve une ReservoirVideException que personne n'attrape, et le script s'arrete ici.
// Ni le dernier affichage, ni la fin du HTML ne sont produits.
// C'est une illustration de la question de la Q4 : « que se passe-t-il quand personne n'attrape l'exception ? ».
$vehicule1->roule(750);

echo $vehicule1->getCaracteristiques() . PHP_EOL;

// On repasse en HTML pour refermer proprement la page.
?>
    </pre>
</body>
</html>
