<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AimeFixture
 */
class AimeFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'aime';
    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id_like' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'username' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
        'id_tweet' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'id_tweet' => ['type' => 'index', 'columns' => ['id_tweet'], 'length' => []],
            'user_like' => ['type' => 'index', 'columns' => ['username'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id_like'], 'length' => []],
            'fk_username_like' => ['type' => 'foreign', 'columns' => ['username'], 'references' => ['users', 'username'], 'update' => 'restrict', 'delete' => 'cascade', 'length' => []],
            'fk_id_tweet_like' => ['type' => 'foreign', 'columns' => ['id_tweet'], 'references' => ['tweets', 'id_tweet'], 'update' => 'restrict', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
        ],
    ];
    // phpcs:enable
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id_like' => 1,
                'username' => 'Lorem ipsum dolor sit amet',
                'id_tweet' => 1,
            ],
        ];
        parent::init();
    }
}
