The repository contains a minimal Nextcloud app.

### Core layout
```
app.php               – (empty placeholder)
appinfo/              – Nextcloud app metadata and route configuration
lib/                  – PHP code (controller)
src/                  – Vue front‑end
webpack.config.js     – Webpack build rules for the Vue code
vendor/               – Composer’s autoloaded dependencies
```

**App declaration.** `appinfo/info.xml` registers the application ID, version, and entry script:

```
3  <id>nextcloudscanworkflow</id>
4  <name>Nextcloud Scan Workflow</name>
5  <description>Preview, rename, and move scanned PDFs</description>
...
16  <entry>app.php</entry>
```

**Routing.** `appinfo/routes.php` exposes one API route:

```
1  <?php
2  return [
3      'routes' => [
4          ['name' => 'file#listFiles', 'url' => '/files', 'verb' => 'GET'],
5      ]
6  ];
```

**Backend controller.** `lib/controller/FileController.php` implements the “listFiles” API that scans the logged‑in user’s “inbox” folder and returns information about PDF files:

```
11  private $rootFolder;
12  private $userId;
...
15  parent::__construct('pdfworkflow', $request);
16  $this->rootFolder = $rootFolder;
17  $this->userId = $userSession->getUser()->getUID();
...
23  public function listFiles(): DataResponse {
24      $folder = $this->rootFolder->getUserFolder($this->userId)->get('inbox');
...
27      foreach ($folder->getDirectoryListing() as $file) {
28          if ($file->getMimeType() === 'application/pdf') {
29              $files[] = [
30                  'name' => $file->getName(),
31                  'path' => $file->getPath(),
32              ];
33          }
34      }

36      return new DataResponse($files);
37  }
```

**Frontend.** The Vue component `src/components/Files.vue` fetches the PDF list from the API, shows a preview in an iframe, and provides rename/move navigation controls:

```
30  methods: {
31    loadFiles() {
32      fetch(OC.generateUrl('/apps/nextcloudscanworkflow/files'))
33        .then(res => res.json())
34        .then(data => {
35          this.files = data;
36          this.filename = this.currentFile.name;
37        });
38    },
...
51    renameAndMove() {
52      alert('Rename & move: ' + this.filename + ' to ' + this.destination);
53    }
...
55  mounted() {
56    this.loadFiles();
57  }
```

`src/main.js` simply mounts this Vue component to `#app`:

```
1  import { createApp } from 'vue';
2  import Files from './components/Files.vue';
...
4  const app = createApp(Files);
5  app.mount('#app');
```

Webpack bundles the Vue code into `js/main.js` when `npm run build` is executed (see `webpack.config.js`).

### Useful points to know
1. **Routing & Controllers** – Nextcloud apps register routes in `appinfo/routes.php`. Controllers live under `lib/` and must follow Nextcloud’s OCP interfaces and annotations (e.g., `@NoAdminRequired`).
2. **Vue Frontend** – The frontend fetches data from those routes. Webpack is configured to compile Vue components.
3. **Autoloading** – Because `composer.json` has no autoload configuration, the `lib/controller/FileController.php` path must match the namespace for Nextcloud’s PSR‑4 autoloader. (Note the presence of both `lib/Controller/` and `lib/controller/`, which might cause confusion.)

### Next steps to explore
- Review Nextcloud’s [app development documentation](https://docs.nextcloud.com/server/latest/developer_manual/app/index.html) for how to integrate navigation entries, provide templates, and register scripts.
- Consider adding an `Application.php` class to initialize the app, register event listeners, or provide a navigation menu entry.
- Implement the rename/move logic for PDFs on the backend (the current Vue button only triggers a JavaScript alert).
- Add tests or development tooling to ensure that the controller and Vue code work together and that PSR‑4 autoloading is set up correctly.

These notes should give newcomers enough context to understand the current structure and begin extending the application.
