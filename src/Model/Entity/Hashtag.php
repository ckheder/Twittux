<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Hashtag Entity
 *
 * @property int $id_hashtag
 * @property string $hashtag
 * @property string $nb_post_hashtag
 */
class Hashtag extends Entity
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
        'hashtag' => true,
        'nb_post_hashtag' => true,
    ];
}
