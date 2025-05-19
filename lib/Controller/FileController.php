<?php
namespace OCA\nextcloudscanworkflow\Controller;

use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\ApiController;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IUserSession;

class FileController extends ApiController {

    /**
     * @NoAdminRequired
     */
    public function listFiles(): DataResponse {
        $folder = $this->rootFolder->getUserFolder($this->userId)->get('inbox');
        $files = [];

        foreach ($folder->getDirectoryListing() as $file) {
            if ($file->getMimeType() === 'application/pdf') {
                $files[] = [
                    'name' => $file->getName(),
                    'path' => $file->getPath(),
                ];
            }
        }

        return new DataResponse($files);
    }
}
