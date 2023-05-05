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
 * This file contains tests for the repository_googleimages class.
 *
 * @package     repository_googleimages
 * @copyright 2023 Meirza <meirza.arson@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_googleimages;

use testable_googleimages;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/googleimages/tests/fixtures/testable_googleimages.php');

class googleimages_test extends \basic_testcase {

    /** @var testable_googleimages google images class. */
    protected testable_googleimages $googleimages;

    protected function setUp(): void {
        $this->googleimages = new testable_googleimages('123', '456');
    }

    /**
     * Test test_search_images() by searching through some existing content.
     *
     * @dataProvider get_api_content
     * @covers \repository/googleimages/googleimages.php::search_images
     * @param string $data dummy data from API
     * @param string $expected expected value
     */
    public function test_search_images(string $data, string $expected) {
        $result = $this->googleimages->search_images_test($data);
        $this->assertEquals($result[0]['title'], $expected);
    }

    /**
     * Data provider for test_search_images().
     *
     * @return array
     */
    public function get_api_content(): array {
        // For testing purposes, we only use the primary data from the API, and excluding that is not necessary.
        // Each array has two values. First is the data from API, and the second is the expected value.
        return [
            'Search for sambal mentah as the keyword' => [
                '{
                    "items": [
                        {
                            "kind": "customsearch#result",
                            "title": "Fresh Chili Sauce (Sambal Mentah) *Vegan Recipe by Aini - Cookpad",
                            "htmlTitle": "Fresh Chili Sauce (\u003cb\u003eSambal Mentah\u003c/b\u003e) *Vegan Recipe by Aini - Cookpad",
                            "link": "https://img-global.cpcdn.com/recipes/1bff5a018f38386d/680x482cq70/fresh-chili-sauce-sambal-mentah-vegan-recipe-main-photo.jpg",
                            "displayLink": "cookpad.com",
                            "snippet": "Fresh Chili Sauce (Sambal Mentah) *Vegan Recipe by Aini - Cookpad",
                            "htmlSnippet": "Fresh Chili Sauce (\u003cb\u003eSambal Mentah\u003c/b\u003e) *Vegan Recipe by Aini - Cookpad",
                            "mime": "image/jpeg",
                            "fileFormat": "image/jpeg",
                            "image": {
                            "contextLink": "https://cookpad.com/uk/recipes/11324122-fresh-chili-sauce-sambal-mentah-vegan",
                            "height": 482,
                            "width": 680,
                            "byteSize": 33473,
                            "thumbnailLink": "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDdQG34aE7UphcPz8Voc3XqvNDytYSpBVNF9ZXIthguJGokesSvdaDfRk&s",
                            "thumbnailHeight": 99,
                            "thumbnailWidth": 139
                            }
                        },
                        {
                            "kind": "customsearch#result",
                            "title": "ENAK BANGET BIKIN NAMBAH NASI TERUS‼️ SAMBAL TERASI MENTAH ...",
                            "htmlTitle": "ENAK BANGET BIKIN NAMBAH NASI TERUS‼️ \u003cb\u003eSAMBAL\u003c/b\u003e TERASI \u003cb\u003eMENTAH\u003c/b\u003e ...",
                            "link": "https://pic-bstarstatic.akamaized.net/ugc/5593febb1dd4ce291c246aacac85559e8d953824.jpg",
                            "displayLink": "www.bilibili.tv",
                            "snippet": "ENAK BANGET BIKIN NAMBAH NASI TERUS‼️ SAMBAL TERASI MENTAH ...",
                            "htmlSnippet": "ENAK BANGET BIKIN NAMBAH NASI TERUS‼️ \u003cb\u003eSAMBAL\u003c/b\u003e TERASI \u003cb\u003eMENTAH\u003c/b\u003e ...",
                            "mime": "image/jpeg",
                            "fileFormat": "image/jpeg",
                            "image": {
                            "contextLink": "https://www.bilibili.tv/en/video/2004501053",
                            "height": 720,
                            "width": 1280,
                            "byteSize": 125914,
                            "thumbnailLink": "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQP2rN-Xi2NCZz71xL4SbfAi2ERIysA0NbrAPF9uDNTg_BMf1VeR9o78g&s",
                            "thumbnailHeight": 84,
                            "thumbnailWidth": 150
                            }
                        },
                        {
                            "kind": "customsearch#result",
                            "title": "Chili Sauce with Kaffir lime leaves (Sambal jeruk purut) Recipe by ...",
                            "htmlTitle": "Chili Sauce with Kaffir lime leaves (\u003cb\u003eSambal\u003c/b\u003e jeruk purut) Recipe by ...",
                            "link": "https://img-global.cpcdn.com/recipes/e01077d256959970/680x482cq70/chili-sauce-with-kaffir-lime-leaves-sambal-jeruk-purut-recipe-main-photo.jpg",
                            "displayLink": "cookpad.com",
                            "snippet": "Chili Sauce with Kaffir lime leaves (Sambal jeruk purut) Recipe by ...",
                            "htmlSnippet": "Chili Sauce with Kaffir lime leaves (\u003cb\u003eSambal\u003c/b\u003e jeruk purut) Recipe by ...",
                            "mime": "image/jpeg",
                            "fileFormat": "image/jpeg",
                            "image": {
                            "contextLink": "https://cookpad.com/pk/recipes/13784586-chili-sauce-with-kaffir-lime-leaves-sambal-jeruk-purut",
                            "height": 482,
                            "width": 680,
                            "byteSize": 56174,
                            "thumbnailLink": "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcReMsfhE2XmIgVJNnIEwXS0WOlGLZbzhvsUOOa_c2E6_W8y5VJdWz-xGQ&s",
                            "thumbnailHeight": 99,
                            "thumbnailWidth": 139
                            }
                        }
                    ]
                }',
                'fresh-chili-sauce-sambal-mentah-vegan-recipe-main-photo.jpg'
            ]
        ];
    }
}
