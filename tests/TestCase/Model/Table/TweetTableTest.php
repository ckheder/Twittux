<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TweetTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TweetTable Test Case
 */
class TweetTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TweetTable
     */
    protected $Tweet;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Tweet',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tweet') ? [] : ['className' => TweetTable::class];
        $this->Tweet = TableRegistry::getTableLocator()->get('Tweet', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Tweet);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
