<?php
require_once 'vfsStream/vfsStream.php';
require_once dirname(__FILE__)  . '/../Crunch.php';

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
    $crunch = new Crunch(vfsStream::url('varLogDir/accessLog/access.log'));
    $this->assertTrue($crunch instanceof Crunch);
  }

  /**
    * @expectedException InvalidArgumentException
    */

  function testCreateACrunchWithFakeLogFile() {
    $crunch = new Crunch(vfsStream::url('emptyDir/someFakeFile.log'));
    $this->setExpectedException('InvalidArgumentException', 'Problem reading file');
  }

  /**
   * @expectedException Exception
   */
  function testCrunchSliceTooBig() {
    $crunch = new Crunch(vfsStream::url('varLogDir/accessLog/access.log'));
    $crunch->rawRead($crunch::MAX_SLICE + 1);
    $this->setExpectedException('Exception', 'Slice too big!');
  }

  function testCrunchRawRead() {
    $crunch = new Crunch(vfsStream::url('varLogDir/accessLog/access.log'));
    $d = $crunch->rawRead(1024);
    $this->assertEquals(1024, strlen($d));
  }

  function testCrunchRawReadInvalidSlice() {
    $crunch = new Crunch(vfsStream::url('varLogDir/accessLog/access.log'));
    $d = $crunch->rawRead(-19);
    $this->assertEquals(1024, strlen($d));
  }

  function testCrunchRawReadVerySmallFile() {
    $crunch = new Crunch(vfsStream::url('varLogDir/accessLog/access_very_small.log'));
    $d = $crunch->rawRead(1024);
    $this->assertLessThanOrEqual(1024, strlen($d));
  }

  function testCrunchRawReadEmptyFile() {
    $crunch = new Crunch(vfsStream::url('varLogDir/accessLog/access_empty.log'));
    $d = $crunch->rawRead(1024);
    $this->assertNull($d);
  }

}
