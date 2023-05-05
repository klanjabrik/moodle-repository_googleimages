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

use repository_googleimages\googleimages;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');
require_once(__DIR__ . '/googleimages.php');

/**
 * repository_googleimages class
 * This is a class used to browse images from google search
 *
 * @since Moodle 4.2.0
 * @package    repository_googleimages
 * @copyright  2023 Meirza <meirza.arson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_googleimages extends repository {

    /** @var string keyword search. */
    protected string $keyword = '';

    /** @var string image size. */
    protected string $imgsize = 'large';

    /** @var googleimages google images class. */
    protected googleimages $googleimages;

    /**
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = []) {
        parent::__construct($repositoryid, $context, $options);

        $this->googleimages = new googleimages($this->get_option('apikey'), $this->get_option('searchengineid'));
    }

    /**
     * Get listing
     *
     * @param string $path this parameter can a folder name, or a identification of folder
     * @param int $page the page number of file list
     * @return array
     */
    public function get_listing($path = '', $page = '') {
        $list = array();
        $list['page'] = (int)$page;
        if ($list['page'] < 1) {
            $list['page'] = 1;
        }
        $list['list'] = $this->googleimages->search_images($this->keyword, $this->imgsize, $list['page'] - 1);
        $list['nologin'] = true;
        $list['norefresh'] = true;
        $list['nosearch'] = true;
        if (!empty($list['list'])) {
            $list['pages'] = -1; // Means we don't know exactly how many pages there are but we can always jump to the next page.
        } else if ($list['page'] > 1) {
            $list['pages'] = $list['page']; // No images available on this page, this is the last page.
        } else {
            $list['pages'] = 0; // No paging.
        }
        return $list;
    }

    /**
     * Check whether the google images form search has been populated.
     *
     * @return bool
     */
    public function check_login() {
        global $SESSION;

        $this->keyword = optional_param('googleimages_keyword', '', PARAM_RAW);
        if (empty($this->keyword)) {
            $this->keyword = optional_param('s', '', PARAM_RAW);
        }
        $sesskeyword = 'googleimages_'.$this->id.'_keyword';
        if (empty($this->keyword) && optional_param('page', '', PARAM_RAW)) {
            // This is the request of another page for the last search, retrieve the cached keyword.
            if (isset($SESSION->{$sesskeyword})) {
                $this->keyword = $SESSION->{$sesskeyword};
            }
        } else if (!empty($this->keyword)) {
            // Save the search keyword in the session so we can retrieve it later.
            $SESSION->{$sesskeyword} = $this->keyword;
        }

        $this->imgsize = optional_param('googleimages_imgsize', '', PARAM_RAW);
        if (empty($this->imgsize)) {
            $this->imgsize = optional_param('s', '', PARAM_RAW);
        }
        $sessimgsize = 'googleimages_'.$this->id.'_imgsize';
        if (empty($this->imgsize) && optional_param('page', '', PARAM_RAW)) {
            // This is the request of another page for the last search, retrieve the cached imgsize.
            if (isset($SESSION->{$sessimgsize})) {
                $this->imgsize = $SESSION->{$sessimgsize};
            }
        } else if (!empty($this->imgsize)) {
            // Save the search imgsize in the session so we can retrieve it later.
            $SESSION->{$sessimgsize} = $this->imgsize;
        }

        return !empty($this->keyword);
    }

    /**
     * Display search form.
     *
     * @return string
     */
    public function print_login() {

        // Keyword input form.
        $keyword = new stdClass();
        $keyword->label = get_string('keyword', 'repository_googleimages').': ';
        $keyword->id    = 'input_text_keyword';
        $keyword->type  = 'text';
        $keyword->name  = 'googleimages_keyword';
        $keyword->value = '';

        // Image size select form.
        $imgsize = new stdClass();
        $imgsize->label = get_string('imgsize', 'repository_googleimages').': ';
        $imgsize->id    = 'input_text_imgsize';
        $imgsize->type  = 'select';
        $imgsize->name  = 'googleimages_imgsize';
        $imgsize->options  = [
            ["value" => 'large',    "label" => 'LARGE'],
            ["value" => 'icon',     "label" => 'ICON'],
            ["value" => 'small',    "label" => 'SMALL'],
            ["value" => 'medium',   "label" => 'MEDIUM'],
            ["value" => 'xlarge',   "label" => 'XLARGE'],
            ["value" => 'xxlarge',  "label" => 'XXLARGE'],
            ["value" => 'huge',     "label" => 'HUGE']
        ];

        if ($this->options['ajax']) {
            $form = array();
            $form['login'] = array($keyword, $imgsize);
            $form['nologin'] = true;
            $form['norefresh'] = true;
            $form['nosearch'] = true;
            $form['allowcaching'] = false;
            return $form;
        } else {
            echo <<<EOD
<table>
<tr>
<td>{$keyword->label}</td><td><input name="{$keyword->name}" type="text" /></td>
</tr>
</table>
<input type="submit" />
EOD;
        }
    }

    /**
     * Global Search.
     * If this plugin support global search, if this function return
     * true, search function will be called when global searching working.
     * @return bool
     */
    public function global_search() {
        return false;
    }

    /**
     * Search.
     *
     * @return array
     */
    public function search($searchtext, $page = 0) {
        $searchresult = array();
        $searchresult['list'] = $this->googleimages->search_images($searchtext, $this->googleimages::DEFAULT_IMG_SIZE, $page);
        return $searchresult;
    }

    /**
     * Logout.
     * When logout button on file picker is clicked, this function will be called.
     *
     * @return string
     */
    public function logout() {
        return $this->print_login();
    }

    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Return the source information
     *
     * @param stdClass $url
     * @return string|null
     */
    public function get_file_source_info($url) {
        return $url;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }

    /**
     * Add Plugin settings input to Moodle form
     * @param object $mform
     */
    public static function type_config_form($mform, $classname = 'repository') {
        $apikey = get_config('googleimages', 'apikey');
        if (empty($apikey)) {
            $apikey = '';
        }
        $searchengineid = get_config('googleimages', 'searchengineid');
        if (empty($searchengineid)) {
            $searchengineid = '';
        }
        $strrequired = get_string('required');

        $mform->addElement(
            'text',
            'apikey',
            get_string('apikey', 'repository_googleimages'),
            array('value' => $apikey, 'size' => '40')
        );
        $mform->setType('apikey', PARAM_RAW_TRIMMED);
        $mform->addRule('apikey', $strrequired, 'required', null, 'client');
        $mform->addElement('static', null, '',  get_string('information_apikey', 'repository_googleimages'));

        $mform->addElement(
            'text',
            'searchengineid',
            get_string('searchengineid', 'repository_googleimages'),
            array('value' => $searchengineid, 'size' => '40')
        );
        $mform->setType('searchengineid', PARAM_RAW_TRIMMED);
        $mform->addRule('searchengineid', $strrequired, 'required', null, 'client');
        $mform->addElement('static', null, '',  get_string('information_searchengineid', 'repository_googleimages'));
    }

    /**
     * Names of the plugin settings
     * @return array
     */
    public static function get_type_option_names() {
        return array('apikey', 'searchengineid', 'pluginname');
    }

    /**
     * save apikey in config table
     * @param array $options
     * @return boolean
     */
    public function set_option($options = array()) {
        if (!empty($options['apikey'])) {
            set_config('apikey', trim($options['apikey']), 'googleimages');
        }
        unset($options['apikey']);

        if (!empty($options['searchengineid'])) {
            set_config('searchengineid', trim($options['searchengineid']), 'googleimages');
        }
        unset($options['searchengineid']);

        return parent::set_option($options);
    }

    /**
     * get apikey from config table
     *
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if ($config === 'apikey') {
            return trim(get_config('googleimages', 'apikey'));
        } else {
            $options['apikey'] = trim(get_config('googleimages', 'apikey'));
        }

        if ($config === 'searchengineid') {
            return trim(get_config('googleimages', 'searchengineid'));
        } else {
            $options['searchengineid'] = trim(get_config('googleimages', 'searchengineid'));
        }

        return parent::get_option($config);
    }

}
