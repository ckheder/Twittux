<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AimeTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AimeTable Test Case
 */
class AimeTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AimeTable
     */
    protected $Aime;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Aime',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Aime') ? [] : ['className' => AimeTable::class];
        $this->Aime = $this->getTableLocator()->get('Aime', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Aime);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
