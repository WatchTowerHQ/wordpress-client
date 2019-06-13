<?php
/**
 * Author: Code2Prog
 * Date: 2019-06-07
 * Time: 16:03
 */

namespace WhatArmy\Watchtower;


use mysqli;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;
use ZipArchive;

/**
 * Class Backup
 * @package WhatArmy\Watchtower
 */
class Backup
{
    public $backupName;

    /**
     * Backup constructor.
     */
    public function __construct()
    {
        $this->backupName = date('Y_m_d__H_i_s')."_".Utils::random_string();
        add_filter('action_scheduler_queue_runner_batch_size', [$this, 'batch_size']);
        add_filter('action_scheduler_queue_runner_concurrent_batches', [$this, 'concurrent_batches']);
        add_action('add_to_zip', [$this, 'add_to_zip']);

    }

    /**
     * @param $job
     */
    public function add_to_zip($job)
    {
        if (defined('WPE_ISP')) {
            ini_set('memory_limit', '512M');
        }
        $archive_location = WHT_BACKUP_DIR.'/'.$job['zip'].'.zip';
        $zippy = new ZipArchive();
        $zippy->open($archive_location, ZipArchive::CREATE);

        $fileList = json_decode(file_get_contents(WHT_BACKUP_DIR.'/'.$job['data_file']));

        foreach ($fileList as $file) {
            $zippy->addFile(ABSPATH.$file, $file);
        }
        $zippy->close();


        $failed = $this->queue_status('failed');
        $pending = $this->queue_status('pending');
        if ($failed == 0 && $pending == 0 && $job['last'] == true) {
            $this->clean_queue();
            $this->call_headquarter($job['callbackHeadquarter']);
        }
        unlink(WHT_BACKUP_DIR.'/'.$job['data_file']); //remove
    }

    /**
     * @param $concurrent_batches
     * @return int
     */
    public function concurrent_batches($concurrent_batches)
    {
        return 1;
    }

    /**
     * @param $batch_size
     * @return int
     */
    public function batch_size($batch_size)
    {
        return 1;
    }

    /**
     * @return $this
     */
    public function create_backup_dir()
    {
        if (!file_exists(WHT_BACKUP_DIR)) {
            mkdir(WHT_BACKUP_DIR, 0777, true);
        }

        if (!file_exists(WHT_BACKUP_DIR.'/index.html')) {
            @file_put_contents(WHT_BACKUP_DIR.'/index.html',
                file_get_contents(plugin_dir_path(WHT_MAIN).'/stubs/index.html.stub'));
        }

        if (!file_exists(WHT_BACKUP_DIR.'/.htaccess')) {
            @file_put_contents(WHT_BACKUP_DIR.'/.htaccess',
                file_get_contents(plugin_dir_path(WHT_MAIN).'/stubs/htaccess.stub'));
        }

        if (!file_exists(WHT_BACKUP_DIR.'/web.config')) {
            @file_put_contents(WHT_BACKUP_DIR.'/web.config',
                file_get_contents(plugin_dir_path(WHT_MAIN).'/stubs/web.config.stub'));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clean_queue()
    {
        global $wpdb;
        $tasks = $wpdb->get_results('SELECT ID  FROM '.$wpdb->posts.' WHERE post_type = "scheduled-action" AND post_title = "add_to_zip"');

        foreach ($tasks as $task) {
            $task_id = $task->ID;
            $wpdb->delete($wpdb->prefix.'comments',
                ['comment_author' => 'ActionScheduler', 'comment_post_ID' => $task_id]);
            $wpdb->delete($wpdb->prefix.'postmeta',
                ['meta_key' => '_action_manager_schedule', 'post_id' => $task_id]);
            $wpdb->delete($wpdb->prefix.'posts',
                ['post_type' => 'scheduled-action', 'post_title' => 'add_to_zip', 'ID' => $task_id]);
        }
        return $this;
    }

    /**
     * @param $status
     * @return int
     */
    public function queue_status($status)
    {
        global $wpdb;
        $results = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}posts WHERE post_type = 'scheduled-action' AND post_title ='add_to_zip' AND post_status = '".$status."'",
            OBJECT);

        return count($results);
    }

    public function call_headquarter_error($callbackHeadquarterUrl)
    {
        $headquarter = new Headquarter($callbackHeadquarterUrl);
        $headquarter->call('/backup_error', [
            'backup_name' => $this->backupName
        ]);
    }

    /**
     * @param $callbackHeadquarterUrl
     */
    public function call_headquarter($callbackHeadquarterUrl)
    {
        $headquarter = new Headquarter($callbackHeadquarterUrl);
        $headquarter->call('/backup', [
            'backup_name' => $this->backupName
        ]);
    }

    /**
     * @return array
     */
    private function exclusions()
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        $data = file_get_contents(WHT_HEADQUARTER_BACKUP_EX, false, stream_context_create($arrContextOptions));
        $ret = array();

        if (Utils::is_json($data)) {
            foreach (json_decode($data) as $d) {
                if ($d->isContentDir == true) {
                    $p = WP_CONTENT_DIR.'/'.$d->path;
                } else {
                    $p = ABSPATH.$d->path;
                }
                array_push($ret, $p);
            }
        }

        return $ret;
    }

    /**
     * @return $this
     */
    private function create_job_list()
    {
        unlink(WHT_BACKUP_DIR.'/backup.job');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ABSPATH));

        $excludes = $this->exclusions();
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            $path = $file->getPathname();
            if (!Utils::strposa($path, $excludes) && $path != '') {
                file_put_contents(WHT_BACKUP_DIR.'/backup.job', $path.PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
        return $this;
    }

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    private function create_job_part_file($name, $data)
    {
        if (!file_exists(WHT_BACKUP_DIR."/".$name)) {
            file_put_contents(WHT_BACKUP_DIR.'/'.$name, json_encode($data));
        }

        return $name;
    }

    /**
     * @param $callbackHeadquarterUrl
     * @return string
     */
    public function mysqlBackup($callbackHeadquarterUrl)
    {
        try {
            $dump = new \MySQLDump(new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME));
            $dump->save(WHT_BACKUP_DIR.'/'.$this->backupName.'.sql.gz');
            $this->call_headquarter($callbackHeadquarterUrl);
        } catch (\Exception $e) {
            $this->call_headquarter_error($callbackHeadquarterUrl);
        }
        return $this->backupName;
    }

    /**
     * @param $callbackHeadquarterUrl
     * @return string
     */
    public function fileBackup($callbackHeadquarterUrl)
    {
        $this->create_backup_dir();

        if (get_option('watchtower')['file_backup'] == 1) {
            if (!file_exists(WHT_BACKUP_DIR."/backup.job")) {
                $this->create_job_list();
            }

            $file = new SplFileObject(WHT_BACKUP_DIR."/backup.job");
            $ct = 0;
            $arr = [];
            while (!$file->eof()) {
                $f = str_replace(ABSPATH, "", $file->fgets());
                if ($f != '') {
                    array_push($arr, trim($f));
                    $ct++;
                }

                if ($ct == WHT_BACKUP_FILES_PER_QUEUE) {
                    as_schedule_single_action(time(), 'add_to_zip', [
                        'files' => [
                            "data_file" => $this->create_job_part_file('part_'.Utils::random_string(6), $arr),
                            "zip"       => $this->backupName,
                            "last"      => false,
                        ]
                    ]);
                    $arr = [];
                    $ct = 0;
                }
                if ($file->eof()) {
                    as_schedule_single_action(time(), 'add_to_zip', [
                        'files' => [
                            "data_file"           => $this->create_job_part_file('part_'.Utils::random_string(6), $arr),
                            "zip"                 => $this->backupName,
                            "last"                => true,
                            "callbackHeadquarter" => $callbackHeadquarterUrl,
                        ]
                    ]);
                    $arr = [];
                    $ct = 0;
                }
            }
            $file = null;
        }

        return $this->backupName;
    }
}