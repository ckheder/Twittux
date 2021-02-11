<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Conversation Entity
 *
 * @property int $id_conv
 * @property string $user_conv
 * @property string $visible
 */
class Conversation extends Entity
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
      'conversation' => true,
        'user_conv' => true,
        'visible' => true,
        'type_conv' => true,
    ];
}
