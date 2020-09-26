<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AbonnementsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AbonnementsTable Test Case
 */
class AbonnementsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AbonnementsTable
     */
    protected $Abonnements;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Abonnements',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Abonnements') ? [] : ['className' => AbonnementsTable::class];
        $this->Abonnements = TableRegistry::getTableLocator()->get('Abonnements', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Abonnements);

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
