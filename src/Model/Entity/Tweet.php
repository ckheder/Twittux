<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tweet Entity
 *
 * @property int $id_tweet
 * @property string $user_tweet
 * @property string $user_timeline
 * @property string $contenu_tweet
 * @property \Cake\I18n\FrozenTime $created
 * @property int $nb_commentaire
 * @property int $nb_partage
 * @property int $nb_like
 * @property bool $private
 * @property bool $allow_comment
 */
class Tweet extends Entity
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
        'id_tweet' => true,
        'username' => true,
        'contenu_tweet' => true,
        'created' => true,
        'nb_commentaire' => true,
        'nb_partage' => true,
        'nb_like' => true,
        'private' => true,
        'allow_comment' => true,
    ];
}
