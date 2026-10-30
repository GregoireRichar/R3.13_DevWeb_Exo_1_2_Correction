<?php

/**
 * Exercice 1, Q5 : la panne seche.
 *
 * C'est la premiere exception du projet, et celle a lire en detail : les trois autres reprennent la meme anatomie.
 *
 * ------------------------------------------------------------------------
 *  POURQUOI CREER SA PROPRE CLASSE D'EXCEPTION
 * ------------------------------------------------------------------------
 *
 * On pourrait tout signaler avec « throw new Exception("panne seche") ». Ca marcherait. Mais l'appelant n'aurait alors qu'un moyen de savoir ce qui s'est passe : lire le message et esperer qu'il ne change jamais.
 *
 * Avec une classe dediee, le TYPE porte l'information, et l'appelant peut reagir differemment selon la panne :
 *
 *     try {
 *         $voiture->roule(500);
 *     } catch (ReservoirVideException $e) {
 *         // on fait le plein et on repart
 *     } catch (PaiementRefuseException $e) {
 *         // la, faire le plein ne servirait a rien
 *     }
 *
 * C'est exactement ce que fait la boucle de td2.php. Avec une exception generique, il faudrait comparer des chaines de caracteres pour distinguer les deux cas, ce qui est fragile et illisible.
 */

namespace Exceptions;

// Le fichier est dans le namespace Exceptions. Sans cette ligne, le nom « Vehicule » employe plus bas designerait Exceptions\Vehicule, qui n'existe pas. On importe donc la classe Vehicule, qui vit dans le namespace global.
use Vehicule;

/**
 * Noter l'antislash de « \Exception ».
 *
 * Nous sommes dans le namespace Exceptions : ecrire « extends Exception » chercherait une classe Exceptions\Exception, qui n'existe pas. L'antislash initial signifie « reprends a la racine », et designe donc la classe Exception native de PHP. C'est l'erreur la plus frequente quand on commence a ranger ses classes dans des namespaces.
 *
 * Toutes les exceptions de PHP descendent de Throwable, via deux branches qui ne se croisent pas : Exception pour les situations anormales que le programme peut traiter, Error pour les fautes de programmation qu'il faut corriger. Une panne seche est une situation de jeu normale : elle herite donc d'Exception, pas d'Error.
 */
class ReservoirVideException extends \Exception {

    // Une exception peut TRANSPORTER du contexte, et c'est tout son interet.
    // Ici, le vehicule concerne : l'appelant qui attrape l'exception peut lui demander son kilometrage, sa marque, et decider quoi faire.
    //
    // readonly : le contexte d'une exception decrit un instant precis. Le laisser modifiable apres coup n'aurait aucun sens.
    private readonly Vehicule $vehicule;

    public function __construct(Vehicule $vehicule) {
        $this->vehicule = $vehicule;

        // parent::__construct() appelle le constructeur d'Exception, qui renseigne $message, $code et $previous. Sans cet appel, getMessage() renverrait une chaine vide : les proprietes de la classe mere ne seraient jamais initialisees.
        //
        // Le message est fige ici plutot que demande a l'appelant : c'est l'exception qui sait ce qu'elle signale, pas celui qui la leve.
        parent::__construct("Le véhicule est tombé en panne sèche ...");
    }

    /**
     * __toString est appelee automatiquement des qu'un objet est utilise la ou PHP attend une chaine. C'est elle qui permet d'ecrire, dans td1.php :
     *
     *     echo "❌ Erreur ! " . $e;
     *
     * Sans elle, la concatenation leverait « Object of class ... could not be converted to string ». Avec elle, on obtient une ligne courte et lisible, au lieu de la trace d'appel complete.
     *
     * __CLASS__ est une constante magique qui vaut le nom complet de la classe ou la ligne est ecrite, namespace compris — d'ou le « Exceptions\ReservoirVideException » affiche a l'execution.
     */
    public function __toString() {
        return "[" . __CLASS__ . "]: {$this->message}\n";
    }

    /** Donne acces au contexte transporte par l'exception. */
    public function getVehicule(): Vehicule {
        return $this->vehicule;
    }
}
