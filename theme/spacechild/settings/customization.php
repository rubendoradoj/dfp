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

$page = new admin_settingpage('theme_spacechild_customization', get_string('settingscustomization', 'theme_spacechild'));
$name = 'theme_spacechild/hgooglefont';
$heading = get_string('hgooglefont', 'theme_spacechild');
$setting = new admin_setting_heading($name, $heading, format_text(get_string('hgooglefont_desc', 'theme_spacechild'), FORMAT_MARKDOWN));
$page->add($setting);

// Google Font.
$name = 'theme_spacechild/googlefonturl';
$title = get_string('googlefonturl', 'theme_spacechild');
$description = get_string('googlefonturl_desc', 'theme_spacechild');
$default = 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_spacechild/fontheadings';
$title = get_string('fontheadings', 'theme_spacechild');
$description = get_string('fontheadings_desc', 'theme_spacechild');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/fontweightheadings';
$title = get_string('fontweightheadings', 'theme_spacechild');
$description = get_string('fontweightheadings_desc', 'theme_spacechild');
$default = '700';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/fontbody';
$title = get_string('fontbody', 'theme_spacechild');
$description = get_string('fontbody_desc', 'theme_spacechild');
$default = "'Poppins', sans-serif";
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/fontweightregular';
$title = get_string('fontweightregular', 'theme_spacechild');
$description = get_string('fontweightregular_desc', 'theme_spacechild');
$default = '400';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/fontweightmedium';
$title = get_string('fontweightmedium', 'theme_spacechild');
$description = get_string('fontweightmedium_desc', 'theme_spacechild');
$default = '500';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/fontweightbold';
$title = get_string('fontweightbold', 'theme_spacechild');
$description = get_string('fontweightbold_desc', 'theme_spacechild');
$default = '700';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/hgeneral';
$heading = get_string('hgeneral', 'theme_spacechild');
$setting = new admin_setting_heading($name, $heading, format_text(get_string('hgeneral_desc', 'theme_spacechild'), FORMAT_MARKDOWN));
$page->add($setting);

$name = 'theme_spacechild/colorbodybg';
$title = get_string('colorbodybg', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorborder';
$title = get_string('colorborder', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/btnborderradius';
$title = get_string('btnborderradius', 'theme_spacechild');
$description = get_string('empty_desc', 'theme_spacechild');
$setting = new admin_setting_configtext($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/hcolorstxt';
$heading = get_string('hcolorstxt', 'theme_spacechild');
$setting = new admin_setting_heading($name, $heading, format_text(get_string('hcolorstxt_desc', 'theme_spacechild'), FORMAT_MARKDOWN));
$page->add($setting);

$name = 'theme_spacechild/colorbody';
$title = get_string('colorbody', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorbodysecondary';
$title = get_string('colorbodysecondary', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorbodylight';
$title = get_string('colorbodylight', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorheadings';
$title = get_string('colorheadings', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorlink';
$title = get_string('colorlink', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorlinkhover';
$title = get_string('colorlinkhover', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/hcolorsprimary';
$heading = get_string('hcolorsprimary', 'theme_spacechild');
$setting = new admin_setting_heading($name, $heading,
    format_text(get_string('hcolorsprimary_desc', 'theme_spacechild'), FORMAT_MARKDOWN));
$page->add($setting);

$name = 'theme_spacechild/colorprimary600';
$title = get_string('colorprimary600', 'theme_spacechild');
$description = get_string('colorprimary_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary100';
$title = get_string('colorprimary100', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary200';
$title = get_string('colorprimary200', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary300';
$title = get_string('colorprimary300', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary400';
$title = get_string('colorprimary400', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary500';
$title = get_string('colorprimary500', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary700';
$title = get_string('colorprimary700', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary800';
$title = get_string('colorprimary800', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorprimary900';
$title = get_string('colorprimary900', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/hcolorsgrays';
$heading = get_string('hcolorsgrays', 'theme_spacechild');
$setting = new admin_setting_heading($name, $heading, format_text(get_string('hcolorsgrays_desc', 'theme_spacechild'), FORMAT_MARKDOWN));
$page->add($setting);

$name = 'theme_spacechild/colorgray100';
$title = get_string('colorgray100', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray200';
$title = get_string('colorgray200', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray300';
$title = get_string('colorgray300', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray400';
$title = get_string('colorgray400', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray500';
$title = get_string('colorgray500', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray600';
$title = get_string('colorgray600', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray700';
$title = get_string('colorgray700', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray800';
$title = get_string('colorgray800', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_spacechild/colorgray900';
$title = get_string('colorgray900', 'theme_spacechild');
$description = get_string('color_desc', 'theme_spacechild');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
