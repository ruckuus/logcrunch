
<?php
require_once 'vfsStream/vfsStream.php';
$accessLogContent = "
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

    $structure = array(
      'varLogDir' => array(
        'accessLog' => array(
          'access.log' => $accessLogContent
        ),
        'emptyDir' => array(
        )
      )
    );

    vfsStreamWrapper::register();
    vfsStreamWrapper::setRoot(vfsStream::newDirectory('crunchTest'));

    vfsStream::create($structure, vfsStreamWrapper::getRoot());

    var_dump(vfsStreamWrapper::getRoot());

    var_dump (vfsStream::url('crunchTest/varLogDir/accessLog/access.log'));



