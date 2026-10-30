<?php

/**
 * Exercice 1, Q4 : la distance demandee depasse l'autonomie MAXIMALE du
 * modele.
 *
 * ------------------------------------------------------------------------
 *  CETTE EXCEPTION N'EST PAS LEVEE DANS LA VERSION ACTUELLE
 * ------------------------------------------------------------------------
 *
 * Elle est declaree, importee dans Vehicule.php et chargee par td1.php et td2.php, mais aucun « throw » ne la mentionne. Elle correspond a un cas que roule() traite autrement : plutot que de refuser une distance impossible, le vehicule roule jusqu'a la panne seche et leve ReservoirVideException.
 *
 * Les deux modelisations se defendent, et elles ne disent pas la meme chose : *
 *   - ReservoirVideException : « pas maintenant, le reservoir est vide ». Faire le plein et reessayer a du sens.
 *   - DistanceException : « jamais, quel que soit le reservoir ». Demander 500 km a une Zoe qui n'en fera jamais plus de 400 d'une traite est une erreur de l'appelant, pas un incident de parcours. Faire le plein n'y changerait rien.
 *
 * Elle est conservee ici parce qu'elle est le support d'une distinction qui compte, et parce qu'elle montre le seul cas des quatre ou l'exception transporte DEUX informations.
 *
 * Si vous la mettez en service, le controle se place en tete de roule(), avant tout calcul :
 *
 *     if ($distance > $this::AUTONOMIE) {
 *         throw new DistanceException($this, $distance);
 *     }
 */

namespace Exceptions;

use Vehicule;

class DistanceException extends \Exception {
    private readonly Vehicule $vehicule;
    private readonly float $distance;

    /**
     * Ce constructeur est le plus complet des quatre : il reprend la signature de celui d'Exception.
     *
     * $code est un entier libre, utile quand on veut classer des erreurs sans multiplier les classes. $previous sert au CHAINAGE : quand on attrape une exception de bas niveau pour en relancer une plus parlante, on passe l'ancienne en troisieme argument. La trace conserve alors les deux, et on garde la cause premiere sans perdre le sens metier.
     *
     * Le « ? » de ?\Throwable signifie que l'argument accepte null, ce qui est sa valeur par defaut : la plupart des exceptions n'ont pas de cause anterieure.
     */
    public function __construct(Vehicule $vehicule, float $distance, $code = 0, ?\Throwable $previous = null) {
        $this->vehicule = $vehicule;
        $this->distance = $distance;

        // Le message est construit a partir du contexte recu, ce qui le rend beaucoup plus utile au deboguage qu'un texte fige.
        parent::__construct("La distance parcourue est supérieure à l'autonomie du véhicule : {$this->distance} km", $code, $previous);
    }

    public function __toString() {
        return "[" . __CLASS__ . "]: {$this->message}\n";
    }

    public function getVehicule(): Vehicule {
        return $this->vehicule;
    }
}
