<?php

namespace Jidaikobo\Kontiki\Controllers\FileControllerTraits;

use Jidaikobo\Kontiki\Utils\Pagination;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait ListTrait
{
    /**
     * Handles the AJAX request to fetch the file list.
     *
     * This method retrieves a list of files from the model, applies security headers
     * to the response, and then renders a view to display the file list.
     *
     * @return Response
     */
    public function filelist(Request $request, Response $response): Response
    {
        // Initialize Pagination and set total items
        $page = $request->getQueryParams()['page'] ?? 1;
        $itemsPerPage = 10;
        $pagination = new Pagination($page, $itemsPerPage);

        $keyword = $request->getQueryParams()['s'] ?? '';
        $query = $this->model->buildSearchConditions($keyword);
        $totalItems = $query->count();

        $pagination->setTotalItems($totalItems);
        $paginationHtml = $pagination->render(env('BASEPATH', '') . "/filelist");

        $items = $query->limit($pagination->getLimit())
                  ->offset($pagination->getOffset())
                  ->orderBy('created_at', 'desc')
                  ->get()
                  ->map(fn($item) => (array) $item)
                  ->toArray();

        $items = $this->processItemsForList($request, $items);

        $content = $this->view->fetch(
            'forms/incFilelist.php',
            [
                'items' => $items,
                'pagination' => $paginationHtml
            ]
        );

        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'text/html')->withStatus(200);
    }

    private function processItemsForList(Request $request, array $items): array
    {
        foreach ($items as $key => $value) {
            $url = $this->pathToUrl($items[$key]['path']);
            $items[$key]['imageOrLink'] = $this->renderImageOrLink($url, $items[$key]['description'] ?? '');
            $items[$key]['url'] = $url;
            $items[$key]['description'] = $items[$key]['description'] ?? ''; // don't use null
        }
        return $items;
    }

    /**
     * Render an image or a link based on the provided URL.
     *
     * @param string $url The input URL, either an image URL or a standard URL.
     * @param string|null $desc description text.
     * @return string The generated HTML.
     */
    private function renderImageOrLink(string $url, ?string $desc): string
    {
      // Check if the URL is an image URL (basic check based on file extension)
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));

        if (isset($pathInfo['extension']) && in_array(strtolower($pathInfo['extension']), $imageExtensions)) {
          // Return an <img> tag for images
            $descText = e($desc);
            $imgSrc = e($url);
            return '<img src="' . $imgSrc . '" alt="' . __('enlarge_x', 'Enlarge :name', ['name' => $descText]) . '" class="clickable-image img-thumbnail" tabindex="0">';
        }

      // Otherwise, return an <a> tag for links
        $linkHref = e($url);

        $extension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : null;

        switch ($extension) {
            case 'pdf':
                $class = 'bi-filetype-pdf';
                break;
            case 'zip':
                $class = 'bi-file-zip';
                break;
            default:
                $class = 'bi-file-text';
                break;
        }

        return '<a href="' . $linkHref . '" target="_blank" aria-label="' . __('downlaod') . '" download class="bi ' . $class . ' display-3"><span class="visually-hidden">' . __('downlaod_x', 'Download :name', ['name' => $desc]) . '</span></a>';
    }

    protected function pathToUrl(string $filePath): string
    {
        $filePath = realpath($filePath);
        $uploadDir = realpath(env('PROJECT_PATH', '') . env('UPLOADDIR'));
        $uploadBaseUrl = rtrim(env('BASEURL'), '/') . rtrim(env('BASEURL_UPLOAD_DIR'), '/');

        if (strpos($filePath, $uploadDir) === 0) {
            $relativePath = ltrim(str_replace($uploadDir, '', $filePath), '/');
            return $uploadBaseUrl . '/' . $relativePath;
        }
        throw new \InvalidArgumentException('The file path is not inside the upload directory.');
    }
}
