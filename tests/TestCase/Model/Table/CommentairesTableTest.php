<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CommentairesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CommentairesTable Test Case
 */
class CommentairesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CommentairesTable
     */
    protected $Commentaires;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Commentaires',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Commentaires') ? [] : ['className' => CommentairesTable::class];
        $this->Commentaires = TableRegistry::getTableLocator()->get('Commentaires', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Commentaires);

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
