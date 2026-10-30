<?php

/**
 * Exercice 2, Q1 et Q4 : la seconde classe fille.
 *
 * Meme structure que VehiculeDiesel, deux constantes et rien d'autre. Mais c'est celle-ci qui revele les erreurs, parce que son autonomie est la plus faible des trois.
 *
 * Le test de la Q1 : faire rouler la Zoe sur 500 km.
 *
 *   - Si la classe mere lit $this::AUTONOMIE ou static::AUTONOMIE, elle obtient 400, s'arrete a la panne seche et le signale.
 *   - Si elle lit self::AUTONOMIE, elle obtient 800. La Zoe parcourt alors tranquillement ses 500 km et annonce 300 km d'autonomie restante. Aucune erreur, aucun avertissement : juste un resultat faux.
 *
 * Ce second cas est le plus instructif des deux. Un bug qui plante se corrige en dix minutes ; un bug qui repond faux peut survivre des mois.
 */
class VehiculeElectrique extends Vehicule {
    protected const AUTONOMIE = 400;
    protected const CARBUTYPE = Carburant::Electricite;

    public function __construct(string $marque, int $puissance, float $kilometrage)
    {
        parent::__construct($marque, $puissance, $kilometrage);
    }
}
