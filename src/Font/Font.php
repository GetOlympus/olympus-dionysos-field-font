<?php

namespace GetOlympus\Dionysos\Field;

use GetOlympus\Zeus\Field\Field;

/**
 * Builds Font field.
 *
 * @package    DionysosField
 * @subpackage Font
 * @author     Achraf Chouk <achrafchouk@gmail.com>
 * @since      0.0.1
 *
 */

class Font extends Field
{
    /**
     * @var array
     */
    protected $adminscripts = ['wp-color-picker'];

    /**
     * @var array
     */
    protected $adminstyles = ['wp-color-picker'];

    /**
     * @var array
     */
    protected $fontmodes = ['googlefonts'];

    /**
     * @var string
     */
    protected $script = 'js'.S.'font.js';

    /**
     * @var string
     */
    protected $style = 'css'.S.'font.css';

    /**
     * @var string
     */
    protected $template = 'font.html.twig';

    /**
     * @var string
     */
    protected $textdomain = 'fontfield';

    /**
     * Ajax callback used for specific Field actions.
     *
     * @param  array   $request
     *
     * @return string
     */
    protected function ajaxCallback($request) : string
    {
        // Get search
        $family = wp_unslash($request['family']);
        $mode   = isset($request['mode']) ? $request['mode'] : 'googlefonts';

        // Check modes
        if (!in_array($mode, $this->fontmodes)) {
            return '-1';
        }

        // Get fonts
        $fonts = $this->getFonts($mode);

        // Check fonts
        if (empty($fonts)) {
            return '-1';
        }

        foreach ($fonts['items'] as $font) {
            if ($family !== $font['family']) {
                continue;
            }

            return json_encode([
                'subsets'  => $font['subsets'],
                'variants' => $font['variants'],
            ]);
        }

        return '-1';
    }

    /**
     * Prepare defaults.
     *
     * @return array
     */
    protected function getDefaults() : array
    {
        return [
            'title' => parent::t('font.title', $this->textdomain),
            'default' => [],
            'description' => '',
            'api_key' => '',
            'mode' => 'googlefonts',

            // settings
            'settings' => [
                /**
                 * Color picker settings
                 * @see https://core.trac.wordpress.org/browser/trunk/src/js/_enqueues/lib/color-picker.js
                 */
                'color' => [
                    'defaultColor' => false,
                    'hide'         => true,
                    'palettes'     => true,
                    'width'        => 255,
                    'mode'         => 'hsv',
                    'type'         => 'full',
                    'slider'       => 'horizontal',
                ]
            ],

            // texts
            't_fontfamily_label'        => parent::t('font.fontfamily_label', $this->textdomain),
            't_fontbackup_label'        => parent::t('font.fontbackup_label', $this->textdomain),
            't_fontsubset_label'        => parent::t('font.fontsubset_label', $this->textdomain),
            't_fontvariant_label'       => parent::t('font.fontvariant_label', $this->textdomain),
            't_fontsize_label'          => parent::t('font.fontsize_label', $this->textdomain),
            't_fontlineheight_label'    => parent::t('font.fontlineheight_label', $this->textdomain),
            't_fontletterspacing_label' => parent::t('font.fontletterspacing_label', $this->textdomain),
            't_fontcolor_label'         => parent::t('font.fontcolor_label', $this->textdomain),
            't_fontpreview_label'       => parent::t('font.fontpreview_label', $this->textdomain),

            't_fontfamily_default'  => parent::t('font.fontfamily_default', $this->textdomain),
            't_fontsubset_default'  => parent::t('font.fontsubset_default', $this->textdomain),
            't_fontvariant_default' => parent::t('font.fontvariant_default', $this->textdomain),
            't_fontpreview_default' => parent::t('font.fontpreview_default', $this->textdomain),

            't_fontbackup_placeholder'        => parent::t('font.fontbackup_placeholder', $this->textdomain),
            't_fontsize_placeholder'          => parent::t('font.fontsize_placeholder', $this->textdomain),
            't_fontlineheight_placeholder'    => parent::t('font.fontlineheight_placeholder', $this->textdomain),
            't_fontletterspacing_placeholder' => parent::t('font.fontletterspacing_placeholder', $this->textdomain),
            't_fontcolor_placeholder'         => parent::t('font.fontcolor_placeholder', $this->textdomain),
        ];
    }

    /**
     * Prepare variables.
     *
     * @param  object  $value
     * @param  array   $contents
     *
     * @return array
     */
    protected function getVars($value, $contents) : array
    {
        // Get contents
        $vars = $contents;

        // Mode
        $vars['mode'] = in_array($vars['mode'], $this->fontmodes) ? $vars['mode'] : $this->fontmodes[0];

        // Retrieve field value
        $vars['value'] = !is_array($value) ? [$value] : $value;

        // Retirve all fonts
        $vars['api_key'] = '';
        $vars['fonts'] = $this->getFonts($vars['mode'], $vars['api_key']);

//die('https://github.com/DannyCooper/olympus-google-fonts');
//die('AIzaSyDXzJ13kwOQbPlM2UMGfDW7MQeLQI0lzI4');
//die('https://developers.google.com/fonts/');

        // Update vars
        return $vars;
    }


    /**
     * Return all available fonts.
     *
     * @param   string  $mode
     * @param   string  $api_key
     *
     * @return  array   $fonts
     */
    protected function getFonts($mode, $api_key = '') : array
    {
        $fonts = [];

        // Check mode
        if ('googlefonts' === $mode) {
            $fonts = $this->getFontsFromGoogle($api_key);
        }

        // Check fonts
        if (is_array($fonts) && !empty($fonts)) {
            return $fonts;
        }

        // Return popular fonts from Google
        return [
            'items' => [
                [
                    'category' => 'sans-serif',
                    'family'   => 'Roboto',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','greek-ext','latin-ext','greek','vietnamese'],
                    'variants' => ['100','100italic','300','300italic','regular','italic','500','500italic','700','700italic','900','900italic'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'Open Sans',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','greek-ext','latin-ext','greek','vietnamese'],
                    'variants' => ['300','300italic','regular','italic','600','600italic','700','700italic','800','800italic'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'Lato',
                    'subsets'  => ['latin','latin-ext'],
                    'variants' => ['100','100italic','300','300italic','regular','italic','700','700italic','900','900italic'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'Montserrat',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','latin-ext','vietnamese'],
                    'variants' => ['100','100italic','200','200italic','300','300italic','regular','italic','500','500italic','600','600italic','700','700italic','800','800italic','900','900italic'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'Oswald',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','latin-ext','vietnamese'],
                    'variants' => ['200','300','regular','500','600','700'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'Raleway',
                    'subsets'  => ['latin','latin-ext'],
                    'variants' => ['100','100italic','200','200italic','300','300italic','regular','italic','500','500italic','600','600italic','700','700italic','800','800italic','900','900italic'],
                ],
                [
                    'category' => 'monospace',
                    'family'   => 'Roboto Mono',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','greek-ext','latin-ext','greek','vietnamese'],
                    'variants' => ['100','100italic','300','300italic','regular','italic','500','500italic','700','700italic'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'Poppins',
                    'subsets'  => ['latin','latin-ext','devanagari'],
                    'variants' => ['100','100italic','200','200italic','300','300italic','regular','italic','500','500italic','600','600italic','700','700italic','800','800italic','900','900italic'],
                ],
                [
                    'category' => 'serif',
                    'family'   => 'Merriweather',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','latin-ext','vietnamese'],
                    'variants' => ['300','300italic','regular','italic','700','700italic','900','900italic'],
                ],
                [
                    'category' => 'sans-serif',
                    'family'   => 'PT Sans',
                    'subsets'  => ['cyrillic','cyrillic-ext','latin','latin-ext'],
                    'variants' => ['regular','italic','700','700italic'],
                ],
            ]
        ];
    }

    /**
     * Return all available Google fonts.
     *
     * @param   string  $api_key
     *
     * @return  array   $fonts
     */
    protected function getFontsFromGoogle($api_key) : array
    {
        $fonts = [];

        // Check api key for API
        if (!empty($api_key)) {
            $fonts = $this->getFontsFromGoogleApi($api_key);
        }

        // Check fonts for JSON
        if (empty($fonts)) {
            $fonts = $this->getFontsFromGoogleJson();
        }

        return $fonts;
    }

    /**
     * Return all available Google fonts from API.
     *
     * @param   string  $api_key
     *
     * @return  array   $fonts
     */
    protected function getFontsFromGoogleApi($api_key) : array
    {
        // Define cache path
        $cachepath = defined('CACHEPATH') ? CACHEPATH : OL_ZEUS_PATH.'app'.S.'cache'.S;

        // Get file contents from cache
        if (file_exists($fonts_from_cache = $cachepath.'dionysos-field-font-google-api-'.$api_key.'.php')) {
            return include $fonts_from_cache;
        }

        // Retrieve data from Google
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $json = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key='.$api_key.'&sort=popularity', false, $context);
        $data = json_decode($json, true);

        //Check errors
        if (isset($data['error'])) {
            // Write in file
            file_put_contents($fonts_from_cache, "<?php\n\n/**\n * This file is auto-generated from GetOlympus/Dionysos/Field/Font vendor\n */\n\nreturn [];\n");
            return [];
        }

        // Check items
        if (empty($data) || !isset($data['items']) || empty($data['items'])) {
            // Write in file
            file_put_contents($fonts_from_cache, "<?php\n\n/**\n * This file is auto-generated from GetOlympus/Dionysos/Field/Font vendor\n */\n\nreturn [];\n");
            return [];
        }

        $fonts = [
            'items' => []
        ];

        // Iterate
        foreach ($data['items'] as $font) {
            unset($font['files']);
            $fonts['items'][] = $font;
        }

        // Write in file
        file_put_contents($fonts_from_cache, "<?php\n\n/**\n * This file is auto-generated from GetOlympus/Dionysos/Field/Font vendor\n */\n\nreturn ".var_export($fonts, true).";\n");

        return $fonts;
    }

    /**
     * Return all available Google fonts from JSON file.
     *
     * @return  array   $fonts
     */
    protected function getFontsFromGoogleJson() : array
    {
        // Define cache path
        $cachepath = defined('CACHEPATH') ? CACHEPATH : OL_ZEUS_PATH.'app'.S.'cache'.S;

        // Get file contents from cache
        if (file_exists($fonts_from_cache = $cachepath.'dionysos-field-font-google-json.php')) {
            return include $fonts_from_cache;
        }

        // Build contents
        $filepath = file_get_contents(realpath(dirname(dirname(__FILE__)).S.'Resources'.S.'fonts').S.'googlefonts.json');
        $fonts = json_decode($filepath, true);

        // Check fonts
        if (empty($fonts)) {
            // Write in file
            file_put_contents($fonts_from_cache, "<?php\n\n/**\n * This file is auto-generated from GetOlympus/Dionysos/Field/Font vendor\n */\n\nreturn [];\n");
            return [];
        }

        // Write in file
        file_put_contents($fonts_from_cache, "<?php\n\n/**\n * This file is auto-generated from GetOlympus/Dionysos/Field/Font vendor\n */\n\nreturn ".var_export($fonts, true).";\n");

        return $fonts;
    }
}
