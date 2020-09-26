<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TweetsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TweetsTable Test Case
 */
class TweetsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TweetsTable
     */
    protected $Tweets;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Tweets',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tweets') ? [] : ['className' => TweetsTable::class];
        $this->Tweets = TableRegistry::getTableLocator()->get('Tweets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Tweets);

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
