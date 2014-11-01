<?php
namespace Xety\Cake3Upload\Test\TestCase\Model\Behavior;

use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Xety\Cake3Upload\Model\Behavior\UploadBehavior;

class UploadBehaviorTest extends TestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = ['core.users'];

/**
 * [testBeforeSave description]
 *
 * @return false
 */
	public function testBeforeSave() {
		$table = $this->getMock('Cake\ORM\Table');
		$this->Behavior = new UploadBehavior($table);
	}
}
