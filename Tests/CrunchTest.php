<?php
require_once dirname(__FILE__)  . '/../vendor/autoload.php';

use \org\bovigo\vfs\vfsStream as vfsStream;
use \org\bovigo\vfs\vfsStreamWrapper as vfsStreamWrapper;


class CrunchTest extends PHPUnit_Framework_TestCase {
  protected $accessLogContent = "
    27.0.0.1 - - [26/Jan/2014:00:18:30 +0800] \"GET / HTTP/1.1\" 503 34 \"-\" \"Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.63 Safari/537.31\"\n
127.0.0.1 - - [26/Jan/2014:01:20:08 +0800] \"-\" 400 0 \"-\" \"-\"\n
    27.0.0.1 - - [26/Jan/2014:00:18:30 +0800] \"GET / HTTP/1.1\" 503 34 \"-\" \"Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.63 Safari/537.31\"\n
    27.0.0.1 - - [26/Jan/2014:00:18:30 +0800] \"GET / HTTP/1.1\" 503 34 \"-\" \"Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.63 Safari/537.31\"\n
    27.0.0.1 - - [26/Jan/2014:00:18:30 +0800] \"GET / HTTP/1.1\" 503 34 \"-\" \"Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.63 Safari/537.31\"\n
    27.0.0.1 - - [26/Jan/2014:00:18:30 +0800] \"GET / HTTP/1.1\" 503 34 \"-\" \"Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.63 Safari/537.31\"\n
127.0.0.1 - - [26/Jan/2014:01:20:08 +0800] \"-\" 400 0 \"-\" \"-\"\n
127.0.0.1 - - [26/Jan/2014:01:20:08 +0800] \"-\" 400 0 \"-\" \"-\"\n
127.0.0.1 - - [26/Jan/2014:01:20:08 +0800] \"-\" 400 0 \"-\" \"-\"\n
127.0.0.1 - - [26/Jan/2014:01:20:08 +0800] \"-\" 400 0 \"-\" \"-\"\n
    ";

  function setUp() {
    $structure = array(
      'varLogDir' => array(
        'accessLog' => array(
          'access.log' => $this->accessLogContent,
          'access_very_small.log' => 'SMALL OR EMPTY',
          'access_empty.log' => ''
        ),
        'emptyDir' => array(
        )
      )
    );

    vfsStreamWrapper::register();
    vfsStreamWrapper::setRoot(vfsStream::newDirectory('crunchTest'));
    vfsStream::create($structure, vfsStreamWrapper::getRoot());
  }

  /**
   * Test constructor
   */
  function testCanCreateACrunch() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access.log'));
    $this->assertTrue($crunch instanceof \LogCrunch\Crunch);
  }

  /**
    * @expectedException InvalidArgumentException
    */

  function testCreateACrunchWithFakeLogFile() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/emptyDir/someFakeFile.log'));
    $this->setExpectedException('InvalidArgumentException', 'Problem reading file');
  }

  /**
   * @expectedException Exception
   */
  function testCrunchSliceTooBig() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access.log'));
    $crunch->rawRead($crunch::MAX_SLICE + 1);
    $this->setExpectedException('Exception', 'Slice too big!');
  }

  function testCrunchRawRead() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access.log'));
    $d = $crunch->rawRead(1024);
    $this->assertEquals(1024, strlen($d));
  }

  function testCrunchRawReadInvalidSlice() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access.log'));
    $d = $crunch->rawRead(-19);
    $this->assertEquals(1024, strlen($d));
  }

  function testCrunchRawReadVerySmallFile() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access_very_small.log'));
    $d = $crunch->rawRead(1024);
    $this->assertLessThanOrEqual(1024, strlen($d));
  }

  function testCrunchRawReadEmptyFile() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access_empty.log'));
    $d = $crunch->rawRead(1024);
    $this->assertNull($d);
  }

  function testCrunchToArray() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access.log'));
    $d = $crunch->toArray($crunch->rawRead(2048));
    $this->assertTrue(is_array($d));
  }

  /**
    * @expectedException InvalidArgumentException
    */
  function testCrunchToArrayWithEmptyData() {
    $crunch = new \LogCrunch\Crunch(vfsStream::url('crunchTest/varLogDir/accessLog/access_empty.log'));
    $d = $crunch->toArray($crunch->rawRead(1024));
    $this->setExpectedException('InvalidArgumentException', 'Empty data.');
  }

}
