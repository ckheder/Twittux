<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setting Entity
 *
 * @property int $id
 * @property string $username
 * @property string $type_profil
 * @property string $notif_message
 * @property string $notif_citation
 * @property string $notif_partage
 * @property string $notif_abonnement
 * @property string $notif_commentaire
 */
class Setting extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'username' => true,
        'type_profil' => true,
        'notif_message' => true,
        'notif_citation' => true,
        'notif_partage' => true,
        'notif_abonnement' => true,
        'notif_commentaire' => true,
    ];
}
