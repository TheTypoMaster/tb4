CREATE TABLE IF NOT EXISTS `#__jfbconnect_user_map` (
	`id` int unsigned NOT NULL auto_increment,
	`j_user_id` INT NOT NULL,
	`fb_user_id` BIGINT NOT NULL,
    `access_token` VARCHAR(255) DEFAULT NULL,
    `authorized` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
);

# 4.2 Not adding the unique to `setting` because we add it later with the install scripts, which can cause a duplicate key
# Not a big deal having 2, but cleaner to only have one.
# In a future version, add it by default ;
CREATE TABLE IF NOT EXISTS `#__jfbconnect_config` (
	`id` int unsigned NOT NULL auto_increment,
	`setting` VARCHAR(50) NOT NULL,
	`value` TEXT,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
);

# 4.1 Create new Requests tables ;
CREATE TABLE IF NOT EXISTS `#__jfbconnect_request` (
	`id` INT unsigned NOT NULL auto_increment,
	`published` TINYINT NOT NULL,
	`title` VARCHAR(50) NOT NULL,
	`message` VARCHAR(250) NOT NULL,
	`destination_url` VARCHAR(200) NOT NULL,
	`thanks_url` VARCHAR(200) NOT NULL,
	`breakout_canvas` TINYINT NOT NULL,
    `created` DATETIME NOT NULL,
	`modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__jfbconnect_notification` (
	`id` INT unsigned NOT NULL auto_increment,
	`fb_request_id` BIGINT NOT NULL,
	`fb_user_to` BIGINT NOT NULL,
	`fb_user_from` BIGINT NOT NULL,
	`jfbc_request_id` INT NOT NULL,
	`status` TINYINT NOT NULL,
	`created` DATETIME NOT NULL,
	`modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
);

# 4.1 Remove unused keys ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_update_status_msg";
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_perm_status_update";
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_perm_email";
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_perm_profile_data";

# 4.0 Remove unused keys ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_api_key";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_comment_max_num" WHERE `setting` = "social_comment_max_num";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_comment_width" WHERE `setting` = "social_comment_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_comment_color_scheme" WHERE `setting` = "social_comment_color_scheme";

UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_comment_max_num" WHERE `setting` = "social_k2_comment_max_num";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_comment_width" WHERE `setting` = "social_k2_comment_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_comment_color_scheme" WHERE `setting` = "social_k2_comment_color_scheme";

UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_layout_style" WHERE `setting` = "social_like_layout_style";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_show_faces" WHERE `setting` = "social_like_show_faces";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_show_send_button" WHERE `setting` = "social_like_show_send_button";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_width" WHERE `setting` = "social_like_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_verb_to_display" WHERE `setting` = "social_like_verb_to_display";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_font" WHERE `setting` = "social_like_font";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_color_scheme" WHERE `setting` = "social_like_color_scheme";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_show_extra_social_buttons" WHERE `setting` = "social_like_show_extra_social_buttons";

UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_layout_style" WHERE `setting` = "social_k2_like_layout_style";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_show_faces" WHERE `setting` = "social_k2_like_show_faces";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_show_send_button" WHERE `setting` = "social_k2_like_show_send_button";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_width" WHERE `setting` = "social_k2_like_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_verb_to_display" WHERE `setting` = "social_k2_like_verb_to_display";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_font" WHERE `setting` = "social_k2_like_font";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_color_scheme" WHERE `setting` = "social_k2_like_color_scheme";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_show_extra_social_buttons" WHERE `setting` = "social_k2_like_show_extra_social_buttons";

# 2.5 Updates from previous versions ;
ALTER TABLE `#__jfbconnect_config` MODIFY COLUMN `value` TEXT;
ALTER TABLE `#__jfbconnect_user_map` MODIFY COLUMN `fb_user_id` BIGINT;

# 3.1 - Update tables for new Profile system ;
# JomSocial ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "js_enable";
UPDATE `#__jfbconnect_config` SET `setting` = "profiles_jomsocial_field_map" WHERE `setting` = "fbFieldMap";
UPDATE `#__jfbconnect_config` SET `setting` = REPLACE(`setting`, 'js', 'profiles_jomsocial') WHERE `setting` LIKE "js_%";

# Community Builder ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "cb_enable";
UPDATE `#__jfbconnect_config` SET `setting` = "profiles_communitybuilder_skip_activation" WHERE `setting` = "skip_cb_activation";
UPDATE `#__jfbconnect_config` SET `setting` = "profiles_communitybuilder_field_map" WHERE `setting` = "cbFieldMap";
UPDATE `#__jfbconnect_config` SET `setting` = REPLACE(`setting`, 'cb', 'profiles_communitybuilder') WHERE `setting` LIKE "cb_%";

# Kunena ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "kunena_enable";
UPDATE `#__jfbconnect_config` SET `setting` = "profiles_kunena_field_map" WHERE `setting` = "kunenaFieldMap";
UPDATE `#__jfbconnect_config` SET `setting` = REPLACE(`setting`, 'kunena', 'profiles_kunena') WHERE `setting` LIKE "kunena_%";