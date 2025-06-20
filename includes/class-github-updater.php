<?php
/**
 * GitHub Plugin Updater
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('GitHubUpdater')) {
    class GitHubUpdater {
        
        private $plugin_file;
        private $plugin_slug;
        private $version;
        private $github_username;
        private $github_repo;
        
        public function __construct($plugin_file, $github_username, $github_repo) {
            $this->plugin_file = $plugin_file;
            $this->plugin_slug = plugin_basename($plugin_file);
            $this->version = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->plugin_slug)['Version'];
            $this->github_username = $github_username;
            $this->github_repo = $github_repo;
            
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
            add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        }
        
        public function check_for_update($transient) {
            if (empty($transient->checked)) {
                return $transient;
            }
            
            $latest_release = $this->get_latest_release();
            
            if ($latest_release && version_compare($this->version, ltrim($latest_release['tag_name'], 'v'), '<')) {
                $transient->response[$this->plugin_slug] = (object) array(
                    'slug' => dirname($this->plugin_slug),
                    'plugin' => $this->plugin_slug,
                    'new_version' => ltrim($latest_release['tag_name'], 'v'),
                    'url' => $latest_release['html_url'],
                    'package' => $latest_release['zipball_url']
                );
            }
            
            return $transient;
        }
        
        public function plugin_info($res, $action, $args) {
            if ($action !== 'plugin_information') {
                return $res;
            }
            
            if (!isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
                return $res;
            }
            
            $latest_release = $this->get_latest_release();
            
            if ($latest_release) {
                $res = (object) array(
                    'name' => 'Timeline Interativa',
                    'slug' => dirname($this->plugin_slug),
                    'version' => ltrim($latest_release['tag_name'], 'v'),
                    'author' => 'Carlos Eduardo',
                    'homepage' => "https://github.com/{$this->github_username}/{$this->github_repo}",
                    'sections' => array(
                        'description' => 'Timeline dinâmica baseado nos códigos do Mert Cukuren (@knyttneve)',
                        'changelog' => $latest_release['body'] ?: 'Atualização disponível'
                    ),
                    'download_link' => $latest_release['zipball_url']
                );
            }
            
            return $res;
        }
        
        private function get_latest_release() {
            $api_url = "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases/latest";
            
            $api_url = add_query_arg('t', time(), $api_url);

            $request = wp_remote_get($api_url, array(
                'timeout' => 10,
                'headers' => array(
                    'User-Agent' => 'Timeline-Plugin-Updater'
                )
            ));
            
            if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
                return json_decode(wp_remote_retrieve_body($request), true);
            }
            
            return false;
        }
    }
} 