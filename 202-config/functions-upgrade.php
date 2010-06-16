<?php

class PROSPER202 { 
	
	function mysql_version() { 
		
		//select the mysql version
		$version_sql = "SELECT version FROM 202_version";
		$version_result = mysql_query($version_sql);
		$version_row = @mysql_fetch_assoc($version_result);
		$mysql_version = $version_row['version'];
		
		//if there is no mysql version, this is an older 1.0.0-1.0.2 release, just return version 1.0.0 for simplicitly sake
		if (!$mysql_version) { $mysql_version = '1.0.2';}
	
		return $mysql_version;
	}
	
	function php_version() { 
		global $version;
		$php_version = $version;
		return $php_version;
	}
}



class UPGRADE {
	

	function upgrade_databases() {
		
		ini_set('max_execution_time', 60*10);
		ini_set('max_input_time', 60*10);

		
		//get the old version
		$mysql_version = PROSPER202::mysql_version();
		$php_version = PROSPER202::php_version();
		
		//if the mysql is 1.0.2, upgrade to 1.0.3
		if ($mysql_version == '1.0.2') { 
		
			//create the new mysql version table
			$sql = "CREATE TABLE IF NOT EXISTS `202_version` (
					  `version` varchar(50) NOT NULL
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$result = _mysql_query($sql);

			//drop the old table
			$sql ="DROP TABLE `202_sort_landings`";
			$result = _mysql_query($sql);
		
			//create the new landing page sorting table
			$sql ="CREATE TABLE IF NOT EXISTS `202_sort_landing_pages` (
				  `sort_landing_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` mediumint(8) unsigned NOT NULL,
				  `landing_page_id` mediumint(8) unsigned NOT NULL,
				  `sort_landing_page_clicks` mediumint(8) unsigned NOT NULL,
				  `sort_landing_page_click_throughs` mediumint(8) unsigned NOT NULL,
				  `sort_landing_page_ctr` decimal(10,2) NOT NULL,
				  `sort_landing_page_leads` mediumint(8) unsigned NOT NULL,
				  `sort_landing_page_su_ratio` decimal(10,2) NOT NULL,
				  `sort_landing_page_payout` decimal(6,2) NOT NULL,
				  `sort_landing_page_epc` decimal(10,2) NOT NULL,
				  `sort_landing_page_avg_cpc` decimal(5,2) NOT NULL,
				  `sort_landing_page_income` decimal(10,2) NOT NULL,
				  `sort_landing_page_cost` decimal(10,2) NOT NULL,
				  `sort_landing_page_net` decimal(10,2) NOT NULL,
				  `sort_landing_page_roi` decimal(10,2) NOT NULL,
				  PRIMARY KEY (`sort_landing_id`),
				  KEY `user_id` (`user_id`),
				  KEY `landing_page_id` (`landing_page_id`),
				  KEY `sort_landing_page_clicks` (`sort_landing_page_clicks`),
				  KEY `sort_landing_page_click_throughs` (`sort_landing_page_click_throughs`),
				  KEY `sort_landing_page_ctr` (`sort_landing_page_ctr`),
				  KEY `sort_landing_page_leads` (`sort_landing_page_leads`),
				  KEY `sort_landing_page_su_ratio` (`sort_landing_page_su_ratio`),
				  KEY `sort_landing_page_payout` (`sort_landing_page_payout`),
				  KEY `sort_landing_page_epc` (`sort_landing_page_epc`),
				  KEY `sort_landing_page_avg_cpc` (`sort_landing_page_avg_cpc`),
				  KEY `sort_landing_page_income` (`sort_landing_page_income`),
				  KEY `sort_landing_page_cost` (`sort_landing_page_cost`),
				  KEY `sort_landing_page_net` (`sort_landing_page_net`),
				  KEY `sort_landing_page_roi` (`sort_landing_page_roi`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;";
			$result = _mysql_query($sql);
			
			 			
			//this is now up to 1.0.3
			$sql = "INSERT INTO 202_version SET version='1.0.3'";
			$result = _mysql_query($sql);
			
			//now set the new mysql version
			$mysql_version = '1.0.3';
			
		}
		
		//upgrade from 1.0.3 to 1.0.4
		if ($mysql_version == '1.0.3') {
			$sql = "UPDATE 202_version SET version='1.0.4'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.0.4';
		} 

		//upgrade from 1.0.4 to 1.0.5
		if ($mysql_version == '1.0.4') {
			$sql = "UPDATE 202_version SET version='1.0.5'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.0.5';
		} 
		
		//upgrade from 1.0.5 to 1.0.6
		if ($mysql_version == '1.0.5') {
			$sql = "UPDATE 202_version SET version='1.0.6'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.0.6';
		} 
		
		//upgrade from 1.0.6 to 1.1.0 - here we had some database modifications to make it scale better.
		if ($mysql_version == '1.0.6') {
			
			//this is upgrading things to BIGINT
			$result = _mysql_query("ALTER TABLE `202_clicks` 			CHANGE `click_id` `click_id` BIGINT UNSIGNED NOT NULL");
			$result = _mysql_query("ALTER TABLE `202_clicks_advance` 	CHANGE `click_id` `click_id` BIGINT UNSIGNED NOT NULL , 
																			CHANGE `keyword_id` `keyword_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `ip_id` `ip_id` BIGINT UNSIGNED NOT NULL");
			$result = _mysql_query(" ALTER TABLE `202_clicks_counter` 	CHANGE `click_id` `click_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT  ");
			$result = _mysql_query(" ALTER TABLE `202_clicks_record` 	CHANGE `click_id` `click_id` BIGINT UNSIGNED NOT NULL  ");
			$result = _mysql_query(" ALTER TABLE `202_clicks_site` 		CHANGE `click_id` `click_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `click_referer_site_url_id` `click_referer_site_url_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `click_landing_site_url_id` `click_landing_site_url_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `click_outbound_site_url_id` `click_outbound_site_url_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `click_cloaking_site_url_id` `click_cloaking_site_url_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `click_redirect_site_url_id` `click_redirect_site_url_id` BIGINT UNSIGNED NOT NULL ");
			$result = _mysql_query(" ALTER TABLE `202_clicks_spy` 		CHANGE `click_id` `click_id` BIGINT UNSIGNED NOT NULL  ");
			$result = _mysql_query(" ALTER TABLE `202_ips` 			CHANGE `ip_id` `ip_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT  ");
			$result = _mysql_query(" ALTER TABLE `202_keywords` 		CHANGE `keyword_id` `keyword_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT  ");
			$result = _mysql_query(" ALTER TABLE `202_last_ips` 		CHANGE `ip_id` `ip_id` BIGINT NOT NULL  ");
			$result = _mysql_query(" ALTER TABLE `202_mysql_errors` 	CHANGE `ip_id` `ip_id` BIGINT UNSIGNED NOT NULL ,
																			CHANGE `site_id` `site_id` BIGINT UNSIGNED NOT NULL ");
			$result = _mysql_query(" ALTER TABLE `202_site_domains` 	CHANGE `site_domain_id` `site_domain_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT  ");
			$result = _mysql_query(" ALTER TABLE `202_site_urls` 		CHANGE `site_url_id` `site_url_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
																			CHANGE `site_domain_id` `site_domain_id` BIGINT UNSIGNED NOT NULL ");
			$result = _mysql_query(" ALTER TABLE `202_sort_ips` CHANGE `ip_id` `ip_id` BIGINT UNSIGNED NOT NULL  ");
			$result = _mysql_query(" ALTER TABLE `202_sort_keywords` CHANGE `keyword_id` `keyword_id` BIGINT UNSIGNED NOT NULL  ");
			$result = _mysql_query(" ALTER TABLE `202_sort_referers` CHANGE `referer_id` `referer_id` BIGINT UNSIGNED NOT NULL  ");
			$result = _mysql_query(" ALTER TABLE `202_users` CHANGE `user_last_login_ip_id` `user_last_login_ip_id` BIGINT UNSIGNED NOT NULL  ");
			
			//mysql version set to 1.1.0 now
			$sql = "UPDATE 202_version SET version='1.1.0'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.1.0';
		}
		
		//upgrade from 1.1.0 to 1.1.1
		if ($mysql_version == '1.1.0') { 
			$sql = "UPDATE 202_version SET version='1.1.1'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.1.1';
		} 
		
		
		//upgrade from 1.1.1 to 1.1.2
		if ($mysql_version == '1.1.1') { 
			$sql = "UPDATE 202_version SET version='1.1.2'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.1.2';
		} 
		
		//upgrade from 1.1.2 to 1.2.0
		if ($mysql_version == '1.1.2') { 
			
			$result = _mysql_query("	 CREATE TABLE IF NOT EXISTS `202_rotations` (
										  `aff_campaign_id` mediumint(8) unsigned NOT NULL,
										  `rotation_num` tinyint(4) NOT NULL,
										  PRIMARY KEY (`aff_campaign_id`)
										) ENGINE=MEMORY DEFAULT CHARSET=latin1; ");
			
			$result = _mysql_query("	INSERT INTO 202_browsers SET browser_id = '9', browser_name = 'Chrome'");
			$result = _mysql_query("	INSERT INTO 202_browsers SET browser_id = '10', browser_name = 'Mobile'");
			$result = _mysql_query("	INSERT INTO 202_browsers SET browser_id = '11', browser_name = 'Console'"); 
			$result = _mysql_query(" 	ALTER TABLE  `202_clicks` CHANGE  `click_cpc`  `click_cpc` DECIMAL( 7, 5 ) NOT NULL "); 
			$result = _mysql_query(" 	ALTER TABLE  `202_trackers` CHANGE  `click_cpc`  `click_cpc` DECIMAL( 7, 5 ) NOT NULL "); 
			
			$result = _mysql_query(" 	ALTER TABLE  `202_users_pref` ADD  `user_cpc_or_cpv` CHAR( 3 ) NOT NULL DEFAULT  'cpc' AFTER  `user_pref_chart` ; "); 
			$result = _mysql_query(" 	ALTER TABLE  `202_users_pref` ADD  `user_keyword_searched_or_bidded` VARCHAR( 255 ) NOT NULL DEFAULT  'searched' AFTER  `user_cpc_or_cpv` ; "); 
			
			
			$result = _mysql_query(" 	ALTER TABLE  `202_aff_campaigns` ADD  `aff_campaign_url_2` TEXT NOT NULL AFTER  `aff_campaign_url` ,
										ADD  `aff_campaign_url_3` TEXT NOT NULL AFTER  `aff_campaign_url_2` ,
										ADD  `aff_campaign_url_4` TEXT NOT NULL AFTER  `aff_campaign_url_3` ,
										ADD  `aff_campaign_url_5` TEXT NOT NULL AFTER  `aff_campaign_url_4` ;");

			$result = _mysql_query(" 	ALTER TABLE  `202_aff_campaigns` CHANGE  `aff_campaign_url`  `aff_campaign_url` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
			
			$result = _mysql_query(" 	ALTER TABLE  `202_aff_campaigns` ADD  `aff_campaign_rotate` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `aff_campaign_time` ;");
			
			$result = _mysql_query(" 	ALTER TABLE`202_sort_breakdowns` CHANGE `sort_breakdown_avg_cpc` `sort_breakdown_avg_cpc` DECIMAL( 7, 5 ) NOT NULL ,
										CHANGE `sort_breakdown_cost` `sort_breakdown_cost` DECIMAL( 13, 5 ) NOT NULL ,
										CHANGE `sort_breakdown_net` `sort_breakdown_net` DECIMAL( 13, 5 ) NOT NULL;");
										
			$result = _mysql_query(" 	ALTER TABLE`202_sort_ips` CHANGE `sort_ip_avg_cpc` `sort_ip_avg_cpc` DECIMAL( 7, 5 ) NOT NULL ,
										CHANGE `sort_ip_cost` `sort_ip_cost` DECIMAL( 13, 5 ) NOT NULL ,
										CHANGE `sort_ip_net` `sort_ip_net` DECIMAL( 13, 5 ) NOT NULL;");
								
			$result = _mysql_query(" 	ALTER TABLE`202_sort_keywords` CHANGE `sort_keyword_avg_cpc` `sort_keyword_avg_cpc` DECIMAL( 7, 5 ) NOT NULL ,
										CHANGE `sort_keyword_cost` `sort_keyword_cost` DECIMAL( 13, 5 ) NOT NULL ,
										CHANGE `sort_keyword_net` `sort_keyword_net` DECIMAL( 13, 5 ) NOT NULL;");
										
			$result = _mysql_query("   ALTER TABLE`202_sort_landing_pages` CHANGE `sort_landing_page_avg_cpc` `sort_landing_page_avg_cpc` DECIMAL( 7, 5 ) NOT NULL ,
										CHANGE `sort_landing_page_cost` `sort_landing_page_cost` DECIMAL( 13, 5 ) NOT NULL ,
										CHANGE `sort_landing_page_net` `sort_landing_page_net` DECIMAL( 13, 5 ) NOT NULL;");
										
			$result = _mysql_query(" 	ALTER TABLE`202_sort_referers` CHANGE `sort_referer_avg_cpc` `sort_referer_avg_cpc` DECIMAL( 7, 5 ) NOT NULL ,
										CHANGE `sort_referer_cost` `sort_referer_cost` DECIMAL( 13, 5 ) NOT NULL ,
										CHANGE `sort_referer_net` `sort_referer_net` DECIMAL( 13, 5 ) NOT NULL;");
										
			$result = _mysql_query(" 	ALTER TABLE`202_sort_text_ads` CHANGE `sort_text_ad_avg_cpc` `sort_text_ad_avg_cpc` DECIMAL( 7, 5 ) NOT NULL ,
										CHANGE `sort_text_ad_cost` `sort_text_ad_cost` DECIMAL( 13, 5 ) NOT NULL ,
										CHANGE `sort_text_ad_net` `sort_text_ad_net` DECIMAL( 13, 5 ) NOT NULL; ");
 

			$sql = "UPDATE 202_version SET version='1.2.0'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.2.0';
		} 
		  
		//upgrade from 1.2.0 to 1,2,1
		if ($mysql_version == '1.2.0') { 
			$sql = "UPDATE 202_version SET version='1.2.1'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.2.1';
		} 
		
		//upgrade from 1.2.1 to 1.3.0
		if ($mysql_version == '1.2.1') {
			
			$result = _mysql_query(" 	ALTER TABLE  `202_users` ADD  `user_api_key` VARCHAR( 255 ) NOT NULL AFTER  `user_pass_time` ; ");
			$result = _mysql_query(" 	ALTER TABLE  `202_users` ADD  `user_stats202_app_key` VARCHAR( 255 ) NOT NULL AFTER  `user_api_key` ; ");
			$sql = "UPDATE 202_version SET version='1.3.0'";	
			$result = _mysql_query($sql);
			$mysql_version = '1.3.0';
		} 
		
		//upgrade from 1.3.0 to 1.3.1
		if ($mysql_version == '1.3.0') {
			
			$result = _mysql_query(" 	ALTER TABLE  `202_clicks_spy` ENGINE = MYISAM "); 
			$result = _mysql_query(" 	ALTER TABLE  `202_last_ips` ENGINE = MYISAM "); 
			
			$sql = "UPDATE 202_version SET version='1.3.1'; ";
			$result = _mysql_query($sql);
			$mysql_version = '1.3.1';
		}
		
		
		//upgrade from 1.3.1 to 1.3.2
		if ($mysql_version == '1.3.1') { 
			
			$result = _mysql_query(" 	ALTER TABLE  `202_clicks_spy` ENGINE = MYISAM "); 
			$result = _mysql_query(" 	ALTER TABLE  `202_last_ips` ENGINE = MYISAM "); 
			
			$sql = "UPDATE 202_version SET version='1.3.2'; ";	
			$result = _mysql_query($sql); 
			$mysql_version = '1.3.2';
		} 
		
		//upgrade from 1.3.2 to 1.4
		if ($mysql_version == '1.3.2') { 

			$result = _mysql_query("	ALTER TABLE 202_users_pref ADD COLUMN `user_tracking_domain` varchar(255) NOT NULL DEFAULT '';");
			$result = _mysql_query("	ALTER TABLE 202_users_pref ADD COLUMN `user_pref_group_1` tinyint(3);");
			$result = _mysql_query("	ALTER TABLE 202_users_pref ADD COLUMN `user_pref_group_2` tinyint(3);");
			$result = _mysql_query("	ALTER TABLE 202_users_pref ADD COLUMN `user_pref_group_3` tinyint(3);");
			$result = _mysql_query("	ALTER TABLE 202_users_pref ADD COLUMN `user_pref_group_4` tinyint(3);");
			
			$result = _mysql_query("	UPDATE 202_aff_campaigns SET aff_campaign_url=CONCAT(aff_campaign_url,'[[subid]]') ");
										
			$result = _mysql_query(" 	CREATE TABLE `202_clicks_tracking` (
										  `click_id` bigint(20) unsigned NOT NULL,
										  `c1` varchar(255) NOT NULL DEFAULT '',
										  `c2` varchar(255) NOT NULL DEFAULT '',
										  `c3` varchar(255) NOT NULL DEFAULT '',
										  `c4` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`click_id`)
										) ENGINE=MyISAM DEFAULT CHARSET=latin1; ");
			
			$sql = "UPDATE 202_version SET version='1.4'; ";
			$result = _mysql_query($sql); 
			$mysql_version = '1.4';
		}
		
		//upgrade from 1.4 to 1.4.1
		if ($mysql_version == '1.4') {
			$result = _mysql_query(" 	CREATE TABLE `202_tracking_c1` (
										  `c1_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
										  `c1` varchar(50) NOT NULL,
										  PRIMARY KEY (`c1_id`),
										  UNIQUE KEY `c1` (`c1`)
										) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ");
			
			$result = _mysql_query(" 	CREATE TABLE `202_tracking_c2` (
										  `c2_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
										  `c2` varchar(50) NOT NULL,
										  PRIMARY KEY (`c2_id`),
										  UNIQUE KEY `c2` (`c2`)
										) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ");
			
			$result = _mysql_query(" 	CREATE TABLE `202_tracking_c3` (
										  `c3_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
										  `c3` varchar(50) NOT NULL,
										  PRIMARY KEY (`c3_id`),
										  UNIQUE KEY `c3` (`c3`)
										) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ");
			
			$result = _mysql_query(" 	CREATE TABLE `202_tracking_c4` (
										  `c4_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
										  `c4` varchar(50) NOT NULL,
										  PRIMARY KEY (`c4_id`),
										  UNIQUE KEY `c4` (`c4`)
										) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ");
			$sql = "UPDATE 202_version SET version='1.4.1'; ";
			$result = _mysql_query($sql); 
			$mysql_version = '1.4.1';
		}
		
		//upgrade from 1.4.1 to 1.4.2
		if ($mysql_version == '1.4.1') {
			$result = _mysql_query(" 	 DROP TABLE `202_clicks_tracking`; ");
			
			$result = _mysql_query(" 	 CREATE TABLE `202_clicks_tracking` (
										  `click_id` bigint(20) unsigned NOT NULL,
										  `c1_id` bigint(20) NOT NULL,
										  `c2_id` bigint(20) NOT NULL,
										  `c3_id` bigint(20) NOT NULL,
										  `c4_id` bigint(20) NOT NULL,
										  PRIMARY KEY (`click_id`)
										) ENGINE=MyISAM DEFAULT CHARSET=latin1; ");
			
			$sql = "UPDATE 202_version SET version='1.4.2'; ";
			$result = _mysql_query($sql); 
			$mysql_version = '1.4.2';
		}
		
		//upgrade from 1.4.2 to 1.4.3
		if ($mysql_version == '1.4.2') {
			
			$result = _mysql_query(" 	ALTER TABLE  `202_clicks_spy` ENGINE = MYISAM "); 
			$result = _mysql_query(" 	ALTER TABLE  `202_last_ips` ENGINE = MYISAM "); 
			
			$sql = "UPDATE 202_version SET version='1.4.3'; ";
			$result = _mysql_query($sql);
			$mysql_version = '1.4.3';
		}
		
		//upgrade from 1.4.3 to 1.5
		if ($mysql_version == '1.4.3') {
			
			$sql = "UPDATE 202_version SET version='1.5'; ";
			$result = _mysql_query($sql);
			$mysql_version = '1.5';
		}
		
		//upgrade from 1.5 to 1.5.1
		if ($mysql_version == '1.5') {
			
			$sql = "UPDATE 202_version SET version='1.5.1'; ";
			$result = _mysql_query($sql);
			$mysql_version = '1.5.1';
		}
		
		return true;
	}
}