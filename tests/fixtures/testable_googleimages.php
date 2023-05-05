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
 * Test support class for testing access_controlled_link_manager.
 *
 * @package    googleimages
 * @copyright  2023 Meirza <meirza.arson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use repository_googleimages\googleimages;
use core\http_client;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/repository/googleimages/googleimages.php');

class testable_googleimages extends googleimages {

    public function search_images_test(string $data): array {

        // Create a mock.
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $data)
        ]);

        // Create http client.
        $this->client = new http_client([
            'mock' => $mock
        ]);

        return $this->search_images('DUMMY TEXT');
    }
}
