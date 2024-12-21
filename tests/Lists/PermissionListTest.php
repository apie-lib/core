<?php
namespace Apie\Tests\Core\Lists;

use Apie\Core\Identifiers\Identifier;
use Apie\Core\Lists\PermissionList;
use Apie\Core\Permissions\AllPermission;
use Apie\Core\Permissions\WriteOnlyPermission;
use Generator;
use PHPUnit\Framework\TestCase;

class PermissionListTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_make_a_string_list()
    {
        $item = new PermissionList([
            new AllPermission(new Identifier('company')),
            'admin:write',
            'company:write',
            new WriteOnlyPermission(new Identifier('company'))
        ]);
        $this->assertEquals(
            [
                'company:read',
                'company:write',
                'admin:write',
            ],
            $item->toStringList()->toArray()
        );
    }

    /**
     * @test
     * @dataProvider provideHasOverlap
     */
    public function it_can_check_for_overlap(bool $expected, array $input, array $compareInput)
    {
        $testItem = new PermissionList($input);
        $this->assertEquals($expected, $testItem->hasOverlap(new PermissionList($compareInput)));
    }

    public function provideHasOverlap(): Generator
    {
        $emptyList = [];
        $publicOnly = [''];
        $list1 = ['test:test'];
        $list2 = ['test:other'];
        $permissionWithPublic = ['test:test', ''];
        $multiplePermissions = ['test:test', 'test:other'];
        yield [false, $emptyList, $emptyList];
        yield [true, $publicOnly, $emptyList];
        yield [false, $list1, $emptyList];
        yield [true, $permissionWithPublic, $emptyList];
        yield [false, $multiplePermissions, $emptyList];

        yield [true, $emptyList, $publicOnly];
        yield [true, $publicOnly, $publicOnly];
        yield [false, $list1, $publicOnly];
        yield [true, $permissionWithPublic, $publicOnly];
        yield [false, $multiplePermissions, $publicOnly];

        yield [false, $emptyList, $list1];
        yield [false, $publicOnly, $list1];
        yield [true, $list1, $list1];
        yield [false, $list2, $list1];
        yield [true, $permissionWithPublic, $list1];
        yield [true, $multiplePermissions, $list1];

        yield [false, $emptyList, $list2];
        yield [false, $publicOnly, $list2];
        yield [false, $list1, $list2];
        yield [true, $list2, $list2];
        yield [false, $permissionWithPublic, $list2];
        yield [true, $multiplePermissions, $list2];

        yield [true, $emptyList, $permissionWithPublic];
        yield [true, $publicOnly, $permissionWithPublic];
        yield [true, $list1, $permissionWithPublic];
        yield [true, $permissionWithPublic, $permissionWithPublic];
        yield [true, $multiplePermissions, $permissionWithPublic];

        yield [false, $emptyList, $multiplePermissions];
        yield [false, $publicOnly, $multiplePermissions];
        yield [true, $list1, $multiplePermissions];
        yield [true, $permissionWithPublic, $multiplePermissions];
        yield [true, $multiplePermissions, $multiplePermissions];

    }
}
