<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 06/07/2015
 * Time: 22:28
 */

use Ark4ne\LightQueue\FileQueue;

if(!function_exists('storage_path')){
    function storage_path(){
        return '';
    }
}
if(!function_exists('base_path')){
    function base_path(){
        return '';
    }
}
class FileQueueTest extends PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $fileQueue = new FileQueue('test');

        $string_test = "string_test";

        $this->assertEquals(0, $fileQueue->fileSize());

        $this->assertTrue($fileQueue->push($string_test));

        $this->assertEquals(13, $fileQueue->fileSize());

        $this->assertTrue($fileQueue->hasNext());

        $this->assertEquals($string_test, $fileQueue->next());

        $this->assertEquals(0, $fileQueue->fileSize());

        $this->assertFalse($fileQueue->hasNext());
    }
}

