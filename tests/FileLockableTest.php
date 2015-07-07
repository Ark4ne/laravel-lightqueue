<?php

/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/07/2015
 * Time: 23:20
 */
class FileLockableTest extends TestCase
{

    public function testCreate()
    {
        $file = new \Ark4ne\LightQueue\Provider\FileLockable('test_file.tester');

        $this->assertTrue(file_exists('test_file.tester'));

        $this->assertFalse($file->isOpen());

        $this->assertFalse($file->isLock());

        $this->assertNotNull($file->handle());
        $this->assertNotNull($file->handle());

        $this->assertTrue($file->lock());

        $this->assertFalse($file->lock());

        fwrite($file->handle(), 'test');

        fflush($file->handle());

        $this->assertTrue($file->unlock());

        $file->close();

    }

    public function testLocker()
    {
        $file = new \Ark4ne\LightQueue\Provider\FileLockable('test_file.tester');

        $file2 = new \Ark4ne\LightQueue\Provider\FileLockable('test_file.tester');


        $this->assertNotNull($file->handle());
        $this->assertNotNull($file2->handle());

        $this->assertTrue($file->lock());
        $this->setExpectedException('Ark4ne\LightQueue\Exception\LightQueueException', "FileLockable::_flock: Can't got lock for queue file !");
        $this->assertFalse($file2->lock());
        $this->assertTrue($file->unlock());

        $this->assertTrue($file2->lock());
        $this->assertTrue($file2->unlock());
        $file->close();
        $file2->close();

    }

}
