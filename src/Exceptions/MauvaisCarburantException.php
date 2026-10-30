<?php

/**
 * Exercice 2, Q3 : le mauvais carburant dans le reservoir.
 *
 * Celle-ci pose une question de conception interessante : pourquoi une quatrieme classe, alors que PaiementRefuseException existe deja et que les deux signalent « le plein n'a pas eu lieu » ?
 *
 * Parce que les deux echecs n'appellent pas la meme reaction. Un paiement refuse se reessaie. Un mauvais carburant, non : reessayer avec le meme pistolet echouera toujours. L'appelant doit pouvoir les distinguer, et c'est le TYPE qui le lui permet, sans jamais lire le message.
 *
 * Regle generale : on cree une classe d'exception par REACTION ATTENDUE, pas par message. Si deux echecs se traitent exactement pareil, une seule classe suffit.
 *
 * A noter : cette exception est levee AVANT toute modification de l'objet. Le vehicule n'a pas ete rempli, et son autonomie n'a pas bouge. Valider d'abord, muter ensuite.
 */

namespace Exceptions;

use Vehicule;

class MauvaisCarburantException extends \Exception {
    private readonly Vehicule $vehicule;

    public function __construct(Vehicule $vehicule) {
        $this->vehicule = $vehicule;

        parent::__construct("Le moteur est complètement fichu !");
    }

    public function __toString() {
        return "[" . __CLASS__ . "]: {$this->message}\n";
    }

    /**
     * Le vehicule transporte ici est particulierement utile : il permet a l'appelant d'ecrire un message complet sans que l'exception ait eu besoin de le connaitre, par exemple
     *
     *     $e->getVehicule()->getCarbuType()
     *
     * pour rappeler quel carburant ce modele accepte reellement.
     */
    public function getVehicule(): Vehicule {
        return $this->vehicule;
    }
}
