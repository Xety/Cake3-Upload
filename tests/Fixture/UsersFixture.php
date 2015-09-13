<?php
namespace Xety\Cake3Upload\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer'],
        'username' => ['type' => 'string', 'length' => 20],
        'avatar' => ['type' => 'string', 'length' => 255, 'default' => '../img/avatar.png'],
        'banner' => ['type' => 'string', 'length' => 255, 'default' => '../img/banner.png'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
            'username' => ['type' => 'unique', 'columns' => ['username']]
        ],
        '_options' => [
            'engine' => 'InnoDB', 'collation' => 'utf8_general_ci'
        ],
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'username' => 'mariano',
            'avatar' => '../img/avatar.png',
            'banner' => '../img/banner.png'
        ],
        [
            'username' => 'larry',
            'avatar' => '../img/avatar.png',
            'banner' => '../img/banner.png'
        ]
    ];
}
