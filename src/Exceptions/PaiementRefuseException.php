<?php

/**
 * Exercice 1, Q5 : le paiement refuse a la station-service.
 *
 * La plus simple des quatre, et elle illustre un point a elle seule : une exception n'a pas besoin de transporter quoi que ce soit.
 *
 * Ici, il n'y a rien d'utile a joindre. Le vehicule n'y est pour rien, la distance n'a aucun rapport : seul compte le fait que l'operation a echoue. La classe se reduit donc a son nom — et son nom suffit, puisque c'est lui que l'appelant attrape.
 *
 * C'est aussi la seule des quatre qui ne laisse pas l'objet dans un etat degrade : le reservoir est reste tel qu'il etait, et il suffit de reessayer. C'est exactement ce que fait la boucle de td2.php — elle compte le refus et repart pour un tour.
 */

namespace Exceptions;

class PaiementRefuseException extends \Exception {

    /**
     * Pas de contexte, pas de propriete : le constructeur se contente de figer le message.
     *
     * Noter au passage l'appel dans Vehicule::faitLePlein() :
     *
     *     throw new PaiementRefuseException;
     *
     * Les parentheses sont facultatives quand on ne passe aucun argument.
     * « new PaiementRefuseException() » est strictement equivalent, et plus courant.
     */
    public function __construct() {
        parent::__construct("Paiement refusé. Les fins de mois sont difficiles ...");
    }

    public function __toString() {
        return "[" . __CLASS__ . "]: {$this->message}\n";
    }
}
