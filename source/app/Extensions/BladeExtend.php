<?php namespace YCMS\Extensions;

/**
 * @author moshihui <86146002@qq.com>
 * 2015/4/11 17:10
 */
class BladeExtend
{

    static $compiles = [
        'demo',
        'layouts',
        'wpposts',
        'wpquery',
        'wpempty',
        'wpend',
        'define',
        'static',
        'lang',
        'eval',
    ];

    public static function register()
    {


        foreach (static::$compiles as $function) {
            if (!method_exists(__CLASS__, $function = 'compile_' . $function)) {
                continue;
            }
            \Blade::extend(function ($view, $compiler) use ($function) {
                return call_user_func([__CLASS__, $function], $view, $compiler);
            });
        }
    }

    public static function ApplyExtends($view)
    {
        global $__env;
        extract(\View::getShared());
        foreach (static::$compiles as $function) {
            if (!method_exists(__CLASS__, $function = 'compile_' . $function)) {
                continue;
            }
            $view = call_user_func([__CLASS__, $function], $view, $__env);

        }
        return $view;
    }

    /**
     * @param $view
     * @return mixed
     * @useage eval($val)
     */
    public static function compile_eval($view)
    {
        return preg_replace('/\@eval\((.+)\)/', '<?php ${1}; ?>', $view);
    }

    /* @datetime($var) */
    /**
     * @param $view
     * @param Blade $compiler
     * @return mixed
     */
    public static function compile_datetime($view, \Blade $compiler)
    {
        $pattern = $compiler->createOpenMatcher('datetime');

        return preg_replace($pattern, '$1<?php echo $2->format(\'m/d/Y H:i\')); ?>', $view);

    }

    protected static function compile_demo($view)
    {
        return preg_replace(["/<\!\-\-\{demo\}\-\->(.*?)<\!\-\-\{\/demo\}\-\->/is", "/<demo[^<>]*?>.*?<\/demo>/is"], "", $view);
    }

    static $lang;

    protected static function compile_lang($view)
    {
        $lang = &self::$lang;
        if (!isset($lang)) {
            $lang = [];
            if ($locallang = @include(storage_path('lang/lang.local.php'))) {
                $lang = (array)$locallang;
            }
            if ($langcache = @include(storage_path('lang/lang.cache.php'))) {
                $lang = array_merge($lang, (array)$langcache);
            }
        }
        return preg_replace_callback("/\[\[\s*(.*?)\s*\]\]/", function ($input) use ($lang) {
            if (!isset($lang[$input[1]])) {
                $lang[$input[1]] = $input[1];
                file_put_contents(storage_path('lang/lang.local.php'), "<?php return " . var_export($lang, true) . ";");
            }
            return "<?php echo ".__CLASS__."::get_lang('" . addslashes($input[1]) . "');?>";
        }, $view);

    }

    public static function get_lang($str)
    {
        return $str;
    }

    public static function compile_continueBreak($view)
    {
        return preg_replace('/(\s*)@(break|continue)(\s*)/', '$1<?php $2; ?>$3', $view);
    }

    /**
     * Rewrites Blade "empty" statements into valid PHP.
     *
     * @param  string $value
     * @return string
     */
    protected static function compile_empty($value)
    {
        return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value);
    }

    /**
     * Rewrites Blade @include statements into valid PHP.
     *
     * @param  string $value
     * @return string
     */
    protected static function compile_includes($value)
    {
        $pattern = static::matcher('include');

        return preg_replace($pattern, '$1<?php echo \View::make$2->with(get_defined_vars())->render(); ?>', $value);
    }

    /**
     * Get the regular expression for a generic Blade function.
     *
     * @param  string $function
     * @return string
     */
    public static function matcher($function)
    {
        return '/(\s*)@' . $function . '(\s*\(.*\))/';
    }

    /**
     * Rewrites Blade "@layout" expressions into valid PHP.
     *
     * @param  string $value
     * @return string
     */
    protected static function compile_layouts($value)
    {
        // If the Blade template is not using "layouts", we'll just return it
        // unchanged since there is nothing to do with layouts and we will
        // just let the other Blade compilers handle the rest.
        if (!starts_with($value, '@layout')) {
            return $value;
        }

        //Strip end of file
        $value = trim($value);

        // First we'll split out the lines of the template so we can get the
        // layout from the top of the template. By convention it must be
        // located on the first line of the template contents.

        /** @todo 2015/06/16 下午4:06 去掉前后空行 foolant */
        $lines = preg_split("/(\r?\n)/", trim($value) );

        $pattern = static::matcher('layout');

        $lines[] = preg_replace($pattern, '$1@include$2', $lines[0]);

        // We will add a "render" statement to the end of the templates and
        // then slice off the "@layout" shortcut from the start so the
        // sections register before the parent template renders.
        return implode("\r\n", array_slice($lines, 1));
    }

    protected static function compile_static($value)
    {
        $pattern = static::matcher('static');
        return preg_replace($pattern, '$1<?php echo '.__CLASS__.'::do_static$2; ?>', $value);
    }

    public static function do_static($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $url = get_stylesheet_directory_uri() . '/static';
        $file = trim($file, '/');
        $ver = wp_get_theme()->get('Version');

        if ($ext == 'js') {
            return <<<HTML
<script type="text/javascript" src="$url/$file?ver=$ver"></script>

HTML;

        } elseif ($ext == 'css') {
            return <<<HTML
<link rel="Stylesheet" type="text/css" href="$url/$file?ver=$ver" />

HTML;

        } else {
            return "$url/$file";
        }
    }

    /**
     * Extract a variable value out of a Blade expression.
     *
     * @param  string $value
     * @return string
     */
    protected static function extract($value, $expression)
    {
        preg_match('/@layout(\s*\(.*\))(\s*)/', $value, $matches);

        return str_replace(array("('", "')"), '', $matches[1]);
    }

    public static function compile_acfrepeater($value)
    {
        $pattern = '/(\s*)@acfrepeater\(((\s*)(.+))\)/';
        $replacement = '$1<?php if ( get_field( $2 ) ) : ';
        $replacement .= 'while ( has_sub_field( $2 ) ) : ?>';

        return preg_replace($pattern, $replacement, $value);
    }

    public static function compile_acfend($value)
    {

        return str_replace('@acfend', '<?php endwhile; endif; ?>', $value);
    }

    /**
     * public static function compile_string( $value, $view = null ) {
     * foreach (static::$compilers as $compiler)
     * {
     * $method = "compile_{$compiler}";
     * $value = static::$method($value, $view);
     * }
     * return $value;
     * }
     */
    /**
     *
     */
    protected static function compile_wpposts($value)
    {

        return str_replace('@wpposts', '<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>', $value);
    }

    /**
     *
     */
    protected static function compile_wpquery($value)
    {

        $pattern = '/(\s*)@wpquery(\s*\(.*\))/';
        $replacement = '$1<?php $bladequery = new WP_Query$2; ';
        $replacement .= 'if ( $bladequery->have_posts() ) : ';
        $replacement .= 'while ( $bladequery->have_posts() ) : ';
        $replacement .= '$bladequery->the_post(); ?> ';

        return preg_replace($pattern, $replacement, $value);
    }

    /**
     *
     */
    protected static function compile_wpempty($value)
    {

        return str_replace('@wpempty', '<?php endwhile; ?><?php else: ?>', $value);
    }

    /**
     *
     */
    protected static function compile_wpend($value)
    {

        return str_replace('@wpend', '<?php endif; wp_reset_postdata(); ?>', $value);
    }

    /**
     *
     */
    protected static function compile_debug($value)
    {

        // Done last
        if (strpos($value, '@debug')) {
            kd($value);
        }

        return $value;
    }

    /**
     *
     */
    protected static function compile_define($value)
    {

        return preg_replace('/\@(define)?(.+)/', '<?php ${2}; ?>', $value);
    }
}
