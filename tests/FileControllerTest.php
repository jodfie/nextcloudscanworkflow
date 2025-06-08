<?php

namespace OCP {
    interface IRequest {}
    interface IUser { public function getUID(); }
    interface IUserSession { public function getUser(); }
}

namespace OCP\Files {
    interface IRootFolder { public function getUserFolder($uid); }
}

namespace OCP\AppFramework\Http {
    class DataResponse {
        private $data;
        public function __construct($data) { $this->data = $data; }
        public function getData() { return $this->data; }
    }
}

namespace OCP\AppFramework {
    class ApiController {
        public function __construct($appName, $request) {}
    }
}

namespace OCA\Nextcloudscanworkflow\Tests {

use OCA\Nextcloudscanworkflow\Controller\FileController;
use OCP\AppFramework\Http\DataResponse;
use PHPUnit\Framework\TestCase;

class FileControllerTest extends TestCase {
    public function testListFilesReturnsOnlyPdf() {
        $pdf = new TestFile('doc.pdf', '/inbox/doc.pdf', 'application/pdf');
        $txt = new TestFile('note.txt', '/inbox/note.txt', 'text/plain');
        $folder = new TestFolder([$pdf, $txt]);
        $root = new TestRootFolder($folder);

        $request = $this->createMock(\OCP\IRequest::class);
        $user = $this->createMock(\OCP\IUser::class);
        $user->method('getUID')->willReturn('user');
        $session = $this->createMock(\OCP\IUserSession::class);
        $session->method('getUser')->willReturn($user);

        $controller = new FileController($request, $root, $session);

        $response = $controller->listFiles();
        $this->assertInstanceOf(DataResponse::class, $response);
        $data = $response->getData();
        $this->assertCount(1, $data);
        $this->assertSame('doc.pdf', $data[0]['name']);
    }
}

class TestRootFolder implements \OCP\Files\IRootFolder {
    private $folder;
    public function __construct($folder) { $this->folder = $folder; }
    public function getUserFolder($uid) {
        return new class($this->folder) {
            private $folder;
            public function __construct($folder) { $this->folder = $folder; }
            public function get($name) { return $this->folder; }
        };
    }
}

class TestFolder {
    private $files;
    public function __construct(array $files) { $this->files = $files; }
    public function getDirectoryListing() { return $this->files; }
}

class TestFile {
    private $name; private $path; private $mime;
    public function __construct($name, $path, $mime) {
        $this->name = $name; $this->path = $path; $this->mime = $mime;
    }
    public function getName() { return $this->name; }
    public function getPath() { return $this->path; }
    public function getMimeType() { return $this->mime; }
}

}
