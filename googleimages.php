<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * googleimages class
 * class for communication with Google search images API
 *
 * @author Meirza <meirza.arson@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace repository_googleimages;

use core\http_client;

define('GOOGLEIMAGES_THUMB_SIZE', 120);
define('GOOGLEIMAGES_NUM', 10); // Do not change this value, increasing the value will give 400 Error.

class googleimages {

    const DEFAULT_IMG_SIZE = 'large';

    /** @var array paramaters to get images. */
    private array $_param = [];

    /** @var string API URL. */
    protected string $api = 'https://customsearch.googleapis.com/customsearch/v1';

    /** @var http_client HTTP Client */
    protected http_client $client;

    public function __construct($apikey, $searchengineid) {

        // Default value.
        $this->_param['key'] = $apikey;
        $this->_param['cx'] = $searchengineid;
        $this->_param['safe'] = 'high'; // Enables SafeSearch filtering.
        $this->_param['rights'] = 'cc_publicdomain%2Ccc_sharealike';
        $this->_param['num'] = GOOGLEIMAGES_NUM;
        $this->_param['searchType'] = 'image';

        // Create http client.
        $this->client = new http_client([
            'base_uri' => $this->api,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Search for images and return photos array.
     *
     * @param string $keyword keyword text
     * @param string $imgsize image size
     * @param int $page page number
     * @param array $params additional query params
     * @return array
     */
    public function search_images(
        string $keyword,
        string $imgsize = self::DEFAULT_IMG_SIZE,
        int $page = 0,
        array $params = []
    ): array {

        $this->_param['q'] = $keyword;
        $this->_param['imgSize'] = $imgsize;
        $this->_param['start'] = ($page * GOOGLEIMAGES_NUM) + 1;
        $this->_param += $params;

        $content = $this->client->request('GET', '', ['query' => $this->_param]);
        $result = json_decode((string)$content->getBody());

        return $this->output($result);
    }

    /**
     * Processing data from google images API
     *
     * @param $data output from google images API
     * @return array
     */
    private function output($data): array {
        $filesarray = array();
        if (!empty($data->items)) {
            foreach ($data->items as $item) {
                // Title will become a file name.
                $linkurl = $item->link;
                $path = parse_url($linkurl, PHP_URL_PATH);
                $title = basename($path);

                $filetype = $item->mime;
                $imagetypes = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');
                if (in_array($filetype, $imagetypes)) {
                    $attrs = array(
                        'image_width' => $item->image->width,
                        'image_height' => $item->image->height,
                        'size' => $item->image->byteSize,
                        'source' => $item->link,
                        'realthumbnail' => $item->link,
                        'realicon' => $item->link,
                        'author' => $item->displayLink,
                        'datemodified' => ''
                        );
                } else {
                    $attrs = array('source' => $item->link);
                }
                $filesarray[] = array(
                    'title'     => $title,
                    'thumbnail' => $item->image->thumbnailLink,
                    'thumbnail_width' => GOOGLEIMAGES_THUMB_SIZE,
                    'thumbnail_height' => GOOGLEIMAGES_THUMB_SIZE,
                    'license' => 'cc-sa',
                    'url' => $item->image->contextLink
                ) + $attrs;
            }
        }
        return $filesarray;
    }
}
