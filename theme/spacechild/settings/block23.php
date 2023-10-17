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
 *
 * @package   theme_spacechild
 * @copyright 2022 - 2023 Marcin Czaja (https://rosea.io)
 * @license   Commercial https://themeforest.net/licenses
 *
 */


defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_spacechild_block23', get_string('settingsblock23', 'theme_spacechild'));

$name = 'theme_spacechild/displayblock23';
$title = get_string('turnon', 'theme_spacechild');
$description = get_string('displayblock23_desc', 'theme_spacechild');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title .
    '<span class="badge badge-sq badge-dark ml-2">Block #23</span>', $description, $default);
$page->add($setting);

$name = 'theme_spacechild/displayhrblock23';
$title = get_string('displayblockhr', 'theme_spacechild');
$description = get_string('displayblockhr_desc', 'theme_spacechild');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/block23class';
$title = get_string('additionalclass', 'theme_spacechild');
$description = get_string('additionalclass_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/block23introtitle';
$title = get_string('blockintrotitle', 'theme_spacechild');
$description = get_string('blockintrotitle_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/block23introcontent';
$title = get_string('blockintrocontent', 'theme_spacechild');
$description = get_string('blockintrocontent_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/FPHTMLCustomCategoryIcon';
$title = get_string('FPHTMLCustomCategoryIcon', 'theme_spacechild');
$description = get_string('FPHTMLCustomCategoryIcon_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/FPHTMLCustomCategoryHeading';
$title = get_string('FPHTMLCustomCategoryHeading', 'theme_spacechild');
$description = get_string('FPHTMLCustomCategoryHeading_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/FPHTMLCustomCategoryContent';
$title = get_string('FPHTMLCustomCategoryContent', 'theme_spacechild');
$description = get_string('FPHTMLCustomCategoryContent_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/FPHTMLCustomCategoryBlockHTML1';
$title = get_string('FPHTMLCustomCategoryBlockHTML1', 'theme_spacechild');
$description = get_string('FPHTMLCustomCategoryBlockHTML1_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/FPHTMLCustomCategoryBlockHTML2';
$title = get_string('FPHTMLCustomCategoryBlockHTML2', 'theme_spacechild');
$description = get_string('FPHTMLCustomCategoryBlockHTML2_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/FPHTMLCustomCategoryBlockHTML3';
$title = get_string('FPHTMLCustomCategoryBlockHTML3', 'theme_spacechild');
$description = get_string('FPHTMLCustomCategoryBlockHTML3_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/block23footercontent';
$title = get_string('blockfootercontent', 'theme_spacechild');
$description = get_string('blockfootercontent_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$settings->add($page);
