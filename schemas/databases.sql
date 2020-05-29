-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2017 at 08:10 AM
-- Server version: 5.7.9
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xt_service`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id` int(11) UNSIGNED NOT NULL,
  `zone` varchar(16) DEFAULT '' COMMENT '区服',
  `channel` varchar(32) DEFAULT '' COMMENT '渠道',
  `type` enum('prepay','spend','login','level') DEFAULT 'prepay' COMMENT '类型',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态',
  `visible` tinyint(4) DEFAULT '1' COMMENT '是否可见',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `url` varchar(255) DEFAULT '' COMMENT 'URL',
  `img` varchar(255) DEFAULT '' COMMENT '图片',
  `img_small` varchar(255) DEFAULT '' COMMENT '小图',
  `custom` varchar(64) DEFAULT '' COMMENT '自定义',
  `sort` int(8) DEFAULT '0' COMMENT '排序',
  `start_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动';

-- --------------------------------------------------------

--
-- Table structure for table `activity_cfg`
--

CREATE TABLE `activity_cfg` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) DEFAULT '0' COMMENT '活动ID',
  `step` int(11) DEFAULT '0' COMMENT '达成级别',
  `prop` varchar(128) DEFAULT '' COMMENT '道具',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `content` varchar(255) DEFAULT '' COMMENT '内容',
  `remark` varchar(255) DEFAULT '' COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动-配置';

-- --------------------------------------------------------

--
-- Table structure for table `card_code`
--

CREATE TABLE `card_code` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(16) NOT NULL DEFAULT '' COMMENT '卡号',
  `topic_id` int(11) NOT NULL DEFAULT '0' COMMENT '主题ID',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='礼品卡号';

-- --------------------------------------------------------

--
-- Table structure for table `card_topic`
--

CREATE TABLE `card_topic` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` enum('cash','discount','prop') DEFAULT 'cash' COMMENT '类型',
  `data` varchar(16) DEFAULT '' COMMENT '量',
  `title` varchar(128) DEFAULT '' COMMENT '标题',
  `intro` varchar(255) DEFAULT '' COMMENT '介绍',
  `limit_times` int(11) DEFAULT '0' COMMENT '次数限制',
  `expired_in` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='礼品卡';

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) UNSIGNED NOT NULL,
  `key` varchar(32) DEFAULT '',
  `value` varchar(64) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invite_code`
--

CREATE TABLE `invite_code` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(16) DEFAULT '',
  `user_id` varchar(16) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邀请';

-- --------------------------------------------------------

--
-- Table structure for table `invite_relation`
--

CREATE TABLE `invite_relation` (
  `id` int(11) UNSIGNED NOT NULL,
  `relation_id` varchar(16) DEFAULT '' COMMENT '用户(发起者)',
  `user_id` varchar(16) DEFAULT '' COMMENT '用户(受邀)',
  `create_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邀请关系';

-- --------------------------------------------------------

--
-- Table structure for table `logs_activity`
--

CREATE TABLE `logs_activity` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) DEFAULT '0' COMMENT '活动ID',
  `cfg_id` int(11) DEFAULT '0' COMMENT '活动配置ID',
  `user_id` varchar(32) DEFAULT '0' COMMENT '用户ID',
  `prop` varchar(32) DEFAULT '' COMMENT '道具',
  `create_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动-日志';

-- --------------------------------------------------------

--
-- Table structure for table `logs_card`
--

CREATE TABLE `logs_card` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(16) DEFAULT '0' COMMENT '卡号',
  `user_id` varchar(16) DEFAULT '' COMMENT '用户ID',
  `item_id` int(11) DEFAULT '0' COMMENT '项目ID',
  `create_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '记录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='礼品卡-日志';

-- --------------------------------------------------------

--
-- Table structure for table `logs_purchase`
--

CREATE TABLE `logs_purchase` (
  `id` int(11) UNSIGNED NOT NULL,
  `transaction` varchar(40) DEFAULT '',
  `user_id` varchar(16) DEFAULT '',
  `gateway` varchar(16) DEFAULT '',
  `amount` decimal(10,2) DEFAULT '0.00',
  `currency` varchar(3) DEFAULT '',
  `product_id` varchar(32) DEFAULT '',
  `status` enum('pending','closed','failed','refund','paid','complete','sandbox') DEFAULT 'pending',
  `ip` varchar(15) DEFAULT '',
  `uuid` varchar(36) DEFAULT '',
  `adid` varchar(40) DEFAULT '',
  `device` varchar(32) DEFAULT '',
  `create_time` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `complete_time` timestamp NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `logs_vote`
--

CREATE TABLE `logs_vote` (
  `id` int(11) UNSIGNED NOT NULL,
  `subject_id` int(11) DEFAULT '0' COMMENT '主题ID',
  `user_id` varchar(32) DEFAULT '0' COMMENT '用户ID',
  `option` tinyint(3) DEFAULT '0' COMMENT '投票选项',
  `create_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动-日志';

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

CREATE TABLE `notice` (
  `id` int(11) UNSIGNED NOT NULL,
  `zone` varchar(16) DEFAULT '' COMMENT '区服',
  `channel` varchar(32) DEFAULT '' COMMENT '渠道',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `img` varchar(255) DEFAULT '' COMMENT '图片',
  `start_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告';

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `product_id` varchar(64) NOT NULL DEFAULT '' COMMENT '产品ID',
  `gateway` varchar(32) NOT NULL DEFAULT '' COMMENT '网关',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '价格',
  `currency` varchar(8) NOT NULL DEFAULT '' COMMENT '币种',
  `coin` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '代币',
  `custom` varchar(32) DEFAULT '' COMMENT '自定义',
  `status` tinyint(3) UNSIGNED DEFAULT '1' COMMENT '状态',
  `sort` int(10) UNSIGNED DEFAULT '0' COMMENT '排序',
  `name` varchar(64) DEFAULT '' COMMENT '产品名称',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `image` varchar(1000) DEFAULT '' COMMENT '图片',
  `package` varchar(64) DEFAULT '' COMMENT '软件包ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products_cfg`
--

CREATE TABLE `products_cfg` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` varchar(64) NOT NULL DEFAULT '' COMMENT '产品id',
  `type` enum('promo','first_purchase') NOT NULL DEFAULT 'promo' COMMENT '普通, 首冲',
  `lowest` int(10) UNSIGNED DEFAULT '0' COMMENT '最低限制',
  `coin` int(10) UNSIGNED DEFAULT '0',
  `prop` varchar(64) DEFAULT '' COMMENT '道具',
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品拓展表';

-- --------------------------------------------------------

--
-- Table structure for table `vote_options`
--

CREATE TABLE `vote_options` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '主题ID',
  `group_id` int(11) DEFAULT '0' COMMENT '分组ID',
  `subject` varchar(128) DEFAULT '' COMMENT '主题-标题',
  `answer` tinyint(3) DEFAULT '0' COMMENT '答案',
  `option_1` varchar(32) DEFAULT '' COMMENT '选项',
  `option_2` varchar(32) DEFAULT '' COMMENT '选项',
  `option_3` varchar(32) DEFAULT '' COMMENT '选项',
  `option_4` varchar(32) DEFAULT '' COMMENT '选项'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vote_topic`
--

CREATE TABLE `vote_topic` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '分组ID',
  `status` tinyint(3) DEFAULT '1',
  `title` varchar(64) DEFAULT '' COMMENT '分组标题',
  `intro` varchar(128) DEFAULT '' COMMENT '介绍',
  `img` varchar(128) DEFAULT '' COMMENT '图片',
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_cfg`
--
ALTER TABLE `activity_cfg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `act_id` (`item_id`);

--
-- Indexes for table `card_code`
--
ALTER TABLE `card_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cdk` (`code`);

--
-- Indexes for table `card_topic`
--
ALTER TABLE `card_topic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `invite_code`
--
ALTER TABLE `invite_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invite_relation`
--
ALTER TABLE `invite_relation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `logs_activity`
--
ALTER TABLE `logs_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logs_card`
--
ALTER TABLE `logs_card`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_purchase`
--
ALTER TABLE `logs_purchase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_tx` (`transaction`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logs_vote`
--
ALTER TABLE `logs_vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `products_cfg`
--
ALTER TABLE `products_cfg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vote_options`
--
ALTER TABLE `vote_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `vote_topic`
--
ALTER TABLE `vote_topic`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `activity_cfg`
--
ALTER TABLE `activity_cfg`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `card_code`
--
ALTER TABLE `card_code`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `card_topic`
--
ALTER TABLE `card_topic`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `invite_code`
--
ALTER TABLE `invite_code`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `invite_relation`
--
ALTER TABLE `invite_relation`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_activity`
--
ALTER TABLE `logs_activity`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_card`
--
ALTER TABLE `logs_card`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_purchase`
--
ALTER TABLE `logs_purchase`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_vote`
--
ALTER TABLE `logs_vote`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `products_cfg`
--
ALTER TABLE `products_cfg`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vote_options`
--
ALTER TABLE `vote_options`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主题ID';
--
-- AUTO_INCREMENT for table `vote_topic`
--
ALTER TABLE `vote_topic`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分组ID';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
