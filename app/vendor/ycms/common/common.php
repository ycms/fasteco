<?php
    /**
    * @package Hello_Dolly
    * @version 1.6
    */
    /*
    Plugin Name: 全局扩展 
    Plugin URI: http://wordpress.org/plugins/hello-dolly/
    Description: 全局扩展增强 1. 替换 gravatar 头像网址(国内无法访问)
    Author: &nbsp;Mo@gbjobs.com
    Version: 1.6
    Author URI: http://ma.tt/
    */
/*
function dmeng_get_https_avatar($avatar) {
    //~ 替换为 https 的域名
    $avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com"), "secure.gravatar.com", $avatar);
    //~ 替换为 https 协议
    $avatar = str_replace("http://", "https://", $avatar);
    return $avatar;
}
add_filter('get_avatar', 'dmeng_get_https_avatar');

*/

add_filter( 'gettext_with_context', 'wpjam_disable_google_fonts', 888, 4);
function wpjam_disable_google_fonts($translations, $text, $context, $domain ) {
    $google_fonts_contexts = array('Open Sans font: on or off','Lato font: on or off','Source Sans Pro font: on or off','Bitter font: on or off');
    if( $text == 'on' && in_array($context, $google_fonts_contexts ) ){
        $translations = 'off';
    }

    return $translations;
}

//add_filter('site_url',  'wpadmin_filter', 10, 3);

//function wpadmin_filter( $url, $path, $orig_scheme ) {
//    $old  = array( "/(wp-admin)/");
//    $admin_dir = WP_ADMIN_DIR;
//    $new  = array($admin_dir);
//    return preg_replace( $old, $new, $url, 1);
//}

/**
 * @see http://www.wpdaxue.com/set-default-admin-color-scheme-for-new-users.html
 */
//对非管理员移除配色方案设置选项
if ( !current_user_can('manage_options') )
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

//为新用户预设默认的后台配色方案
function set_default_admin_color($user_id) {
    $args = array(
      'ID' => $user_id,
      'admin_color' => 'light'
    );
    wp_update_user( $args );
}
add_action('user_register', 'set_default_admin_color');

add_filter('upload_dir', function ($uploads){
    //kd($uploads);
    $upload_path = 'uploads/media';
    $upload_url_path = '/uploads/media';

    if (empty($upload_path) || 'wp-content/uploads' == $upload_path) {
        $uploads['basedir'] = WP_CONTENT_DIR . '/uploads';
    } elseif (0 !== strpos($upload_path, WP_CONTENT_DIR)) {
        $uploads['basedir'] = path_join(WP_CONTENT_DIR, $upload_path);
    } else {
        $uploads['basedir'] = $upload_path;
    }

    $uploads['basedir'] = base_path('uploads/media');

    $uploads['path'] = $uploads['basedir'] . $uploads['subdir'];

    if ($upload_url_path) {
        $uploads['baseurl'] = $upload_url_path;
        $uploads['url'] = $uploads['baseurl'] . $uploads['subdir'];
    }

    return $uploads;
});

//解决上传文件名中文乱码问题
add_filter('sanitize_file_name',function($filename){
    $ext = pathinfo($filename,PATHINFO_EXTENSION);
    $newname = date('YmdHis').floor(microtime()*1000).substr(md5($filename),16).($ext ? ".$ext" : '');
    return $newname;

},10);


foreach(glob(__DIR__.'/libs/*.php') as $pluginfile){
    require_once $pluginfile;
}

show_admin_bar(false);


/**
 * @see http://zmingcx.com/wordpress-4-2-edition-problem.html
 */

function init_smilies(){
    global $wpsmiliestrans;
    $wpsmiliestrans = array(
        ':mrgreen:' => 'icon_mrgreen.gif',
        ':neutral:' => 'icon_neutral.gif',
        ':twisted:' => 'icon_twisted.gif',
        ':arrow:' => 'icon_arrow.gif',
        ':shock:' => 'icon_eek.gif',
        ':smile:' => 'icon_smile.gif',
        ':???:' => 'icon_confused.gif',
        ':cool:' => 'icon_cool.gif',
        ':evil:' => 'icon_evil.gif',
        ':grin:' => 'icon_biggrin.gif',
        ':idea:' => 'icon_idea.gif',
        ':oops:' => 'icon_redface.gif',
        ':razz:' => 'icon_razz.gif',
        ':roll:' => 'icon_rolleyes.gif',
        ':wink:' => 'icon_wink.gif',
        ':cry:' => 'icon_cry.gif',
        ':eek:' => 'icon_surprised.gif',
        ':lol:' => 'icon_lol.gif',
        ':mad:' => 'icon_mad.gif',
        ':sad:' => 'icon_sad.gif',
        '8-)' => 'icon_cool.gif',
        '8-O' => 'icon_eek.gif',
        ':-(' => 'icon_sad.gif',
        ':-)' => 'icon_smile.gif',
        ':-?' => 'icon_confused.gif',
        ':-D' => 'icon_biggrin.gif',
        ':-P' => 'icon_razz.gif',
        ':-o' => 'icon_surprised.gif',
        ':-x' => 'icon_mad.gif',
        ':-|' => 'icon_neutral.gif',
        ';-)' => 'icon_wink.gif',
        '8O' => 'icon_eek.gif',
        ':(' => 'icon_sad.gif',
        ':)' => 'icon_smile.gif',
        ':?' => 'icon_confused.gif',
        ':D' => 'icon_biggrin.gif',
        ':P' => 'icon_razz.gif',
        ':o' => 'icon_surprised.gif',
        ':x' => 'icon_mad.gif',
        ':|' => 'icon_neutral.gif',
        ';)' => 'icon_wink.gif',
        ':!:' => 'icon_exclaim.gif',
        ':?:' => 'icon_question.gif',
    );
}
add_action( 'init', 'init_smilies', 5 );

/**
 * @see http://www.solagirl.net/multi-wordpress-sites-sharing-user-data.html
 * 共用用户表时自动更新权限
 */
add_action('user_register', 'dup_capabilities' );
add_action('wp_authenticate_user',  function($user){
    dup_capabilities($user->ID);
    return $user;
} , 1 );
add_action('profile_update', 'dup_capabilities');
function dup_capabilities( $user_id ){
    //在这里设置数据表前缀，不分主站子站，全部写上即可。
    $addi_prefixs = array('en_');
    $main_prefix = 'wp_';
    //获取该用户权限的值，因为不同角色的值是不同的
    if( $cap_val = get_user_meta( $user_id, $main_prefix.'capabilities', true ) ) {
        if( count( $addi_prefixs ) > 0 ) {
            foreach( $addi_prefixs as $prefix ) {
                add_user_meta( $user_id, $prefix.'capabilities', $cap_val, true );
            }
        }
    }
}