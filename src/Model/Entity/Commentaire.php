<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Commentaire Entity
 *
 * @property int $id_comm
 * @property string $commentaire
 * @property int $id_tweet
 * @property string $username
 * @property \Cake\I18n\FrozenTime $created
 */
class Commentaire extends Entity
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
        'id_comm' =>true,
        'commentaire' => true,
        'id_tweet' => true,
        'username' => true,
        'created' => true,
    ];
}
