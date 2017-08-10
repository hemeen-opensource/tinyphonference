/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : tinyphonference

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-07-04 17:02:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cti_cdr
-- ----------------------------
DROP TABLE IF EXISTS `cti_cdr`;
CREATE TABLE `cti_cdr` (
  `id` varchar(64) COLLATE utf8_bin NOT NULL COMMENT 'UUID',
  `nodeid` varchar(256) COLLATE utf8_bin NOT NULL COMMENT 'IPSC节点ID（格式：区域ID.站ID.IPSC实例ID）',
  `cdrid` varchar(256) COLLATE utf8_bin NOT NULL COMMENT 'CDR 记录ID',
  `processid` varchar(256) COLLATE utf8_bin NOT NULL COMMENT '流水号（全局唯一，IPSC实例启动时开始计算，单个实例期间严格递增）',
  `callid` varchar(256) COLLATE utf8_bin NOT NULL COMMENT '呼叫标识号（节点内全局唯一）',
  `ch` int(11) NOT NULL COMMENT '通道号：因交换机初始化时间不同，通道号可能会变化',
  `cdrcol` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `devno` varchar(256) COLLATE utf8_bin NOT NULL COMMENT '设备号： \n中继：格式 “0:0:1:1”—“交换机号:板号:中继号:通道号”；\nSIP：格式“0:0:1”—“交换机号:板号:通道号”；\nFXO：格式“0:0:1”—“交换机号:板号:通道号”；',
  `ani` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '主叫号码',
  `dnis` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '被叫号码',
  `dnis2` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '原被叫号码',
  `orgcallno` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '原始号码',
  `dir` int(11) NOT NULL COMMENT '呼叫方向 \n0: 呼入\n1: 呼出\n2: 内部呼叫（保留）',
  `devtype` int(11) NOT NULL COMMENT '通道设备类型 \n1: 中继\n2: SIP\n3: H323\n4: 模拟外线\n5: 模拟内线\n10: 逻辑通道',
  `busitype` int(11) DEFAULT NULL,
  `callstatus` int(11) NOT NULL COMMENT '呼通标志 \n0: 呼叫未接通\n1: 呼叫接通',
  `endtype` int(11) NOT NULL COMMENT '结束类型 \n0: 空（初始值，未定义）\n1: 本地拆线\n2: 远端拆线\n3: 设备拆线',
  `ipscreason` int(11) DEFAULT NULL COMMENT '呼叫失败原因：IPSC定义reason值',
  `callfailcause` int(11) DEFAULT NULL COMMENT '呼叫失败原因：设备、SS7、PRI、SIP的失败cause值',
  `callbegintime` datetime NOT NULL COMMENT '开始时间',
  `connectbegintime` datetime DEFAULT NULL COMMENT '应答时间（呼叫未接通时，该时间为空）',
  `callendtime` datetime NOT NULL COMMENT '挂断时间',
  `talkduration` int(11) NOT NULL DEFAULT '0' COMMENT '通话时长（单位秒，应答时间-挂断时间，如果没有应答时间，通话时长为0）',
  `projectid` varchar(256) COLLATE utf8_bin NOT NULL COMMENT '虚拟化项目ID',
  `flowid` varchar(256) COLLATE utf8_bin NOT NULL COMMENT '流程ID',
  `additionalinfo1` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '附加信息1',
  `additionalinfo2` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '附加信息2',
  `additionalinfo3` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '附加信息3',
  `additionalinfo4` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '附加信息4',
  `additionalinfo5` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '附加信息5',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='呼叫详情';

-- ----------------------------
-- Records of cti_cdr
-- ----------------------------

-- ----------------------------
-- Table structure for cti_perf
-- ----------------------------
DROP TABLE IF EXISTS `cti_perf`;
CREATE TABLE `cti_perf` (
  `id` int(11) NOT NULL,
  `nodeid` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT 'IPSC节点ID（格式：区域ID.站ID.IPSC实例ID）',
  `key` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '指标名',
  `time_update` datetime NOT NULL,
  `fval` float DEFAULT NULL COMMENT '指标值-浮点数',
  `ival` int(11) DEFAULT NULL COMMENT '指标值-整数',
  `tval` text COLLATE utf8_bin COMMENT '指标值-文本',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='呼叫性能——记录当前的呼叫性能数据。由CTI服务器定时刷新';

-- ----------------------------
-- Records of cti_perf
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2', '2014_10_12_100000_create_password_resets_table', '1');

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `administrator` tinyint(4) NOT NULL DEFAULT '0' COMMENT '超级管理员 1 为超级管理员',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机号码',
  `qq` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'qq',
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '本次登录ip',
  `login_time` timestamp NULL DEFAULT NULL COMMENT '本次登录时间',
  `last_login_time` timestamp NULL DEFAULT NULL COMMENT '上次登录时间',
  `last_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '上次登录ip',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'user001', '$2y$10$uuXBBB2/wdu88UAV1sfm5e6CjDuTa8fMnhEH00hg0j9vaojFu5nFC', '1', '475647150@qq.com', '13611460986', '475647150', '127.0.0.1', '2017-06-29 09:45:45', '2017-06-29 07:54:30', '127.0.0.1', null, null, null, '2017-06-29 09:45:45');
INSERT INTO `users` VALUES ('2', 'test002', '$2y$10$uuXBBB2/wdu88UAV1sfm5e6CjDuTa8fMnhEH00hg0j9vaojFu5nFC', '0', '308290139@qq.com', '13611460901', null, null, null, null, null, null, null, null, '2017-06-27 08:27:03');
INSERT INTO `users` VALUES ('3', 'chenjj', '$2y$10$dHgqEc64gTAdwV1IjV1qE.BiPpE/IvFbkDm4zh44kR2Z.7JC7N1eG', '0', '123456@qq.com', '13611460986', '123456', '127.0.0.1', '2017-07-04 16:38:44', '2017-07-03 13:42:42', '127.0.0.1', '1121', null, '2017-06-24 05:41:58', '2017-07-04 16:38:44');
