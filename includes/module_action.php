<? 
/*
    Copyright (C) 2013-2015 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

//include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($io_action, "../msg.php", $regex_extra);
    regex_standard($_GET["mac"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
	regex_standard($_GET["php_fpm"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$mac =  strtoupper($_GET['mac']);
$install = $_GET['install'];
$php_fpm = $_GET["php_fpm"];

// SET FPM
if ($php_fpm == "php7" or $php_fpm == "php5") {
    $ss_mode = $service;
    $exec = "/bin/sed -i 's/mod_nginx_fpm.*/mod_nginx_fpm = \\\"".$php_fpm."\\\";/g' ../_info_.php";
    exec_blackbulb($exec);
	
	header("Location: ../index.php");
    exit;
}

if($service == $mod_name) {
    
    if ($action == "start") {
        
	// START MODULE
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
            exec_blackbulb($exec);
            
            $exec = "echo '' > $mod_logs";
            exec_blackbulb($exec);
        }
		
		if ($mod_nginx_fpm == "php7") {
			$exec = "cp vhost-php7.conf vhost/FruityWiFi-www";
			exec_blackbulb($exec);
			
			if (!file_exists("/etc/php/7.0/fpm/pool.d/80.conf") or !file_exists("/etc/php/7.0/fpm/pool.d/443.conf")) {
				$exec = "cp php7-fpm/80.conf /etc/php/7.0/fpm/pool.d/";
				exec_blackbulb($exec);
				$exec = "cp php7-fpm/443.conf /etc/php/7.0/fpm/pool.d/";
				exec_blackbulb($exec);
				$exec = "$php7_fpm -y /etc/php/7.0/fpm/pool.d/80.conf";
				exec_blackbulb($exec);
				$exec = "$php7_fpm -y /etc/php/7.0/fpm/pool.d/443.conf";
				exec_blackbulb($exec);
			}
		} else {
			$exec = "cp vhost-php5.conf vhost/FruityWiFi-www";
			exec_blackbulb($exec);
			
			if (!file_exists("/etc/php5/fpm/pool.d/80.conf") or !file_exists("/etc/php5/fpm/pool.d/443.conf")) {
				$exec = "cp php5-fpm/80.conf /etc/php5/fpm/pool.d/";
				exec_blackbulb($exec);
				$exec = "cp php5-fpm/443.conf /etc/php5/fpm/pool.d/";
				exec_blackbulb($exec);
				$exec = "$php5_fpm -y /etc/php5/fpm/pool.d/80.conf";
				exec_blackbulb($exec);
				$exec = "$php5_fpm -y /etc/php5/fpm/pool.d/443.conf";
				exec_blackbulb($exec);
			}
		}
		
		$exec = "$bin_nginx -c /usr/share/blackbulb/www/modules/nginx/includes/nginx.conf";
        exec_blackbulb($exec);
		
    } else if($action == "stop") {
	
		// STOP MODULE
		$exec = "ps aux|grep -E 'nginx.+/modules/nginx/includes/nginx.conf' | grep -v grep | awk '{print $2}'";
		exec($exec,$output);
		
		$exec = "kill " . $output[0];
		exec_blackbulb($exec);
	
		// COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
            exec_blackbulb($exec);
            
            $exec = "echo '' > $mod_logs";
            exec_blackbulb($exec);
        }
	
    }

}

if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    exec_blackbulb($exec);
    
    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_blackbulb($exec);

    header("Location: ../../install.php?module=$mod_name");
    exit;
}

$filename = $file_users;

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header("Location: ../../action.php?page=$mod_name");
}

?>