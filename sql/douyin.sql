/*
Navicat MySQL Data Transfer

Source Server         : rycs
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : douyin

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-07-09 20:59:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for douyin
-- ----------------------------
DROP TABLE IF EXISTS `douyin`;
CREATE TABLE `douyin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sharetitle` varchar(255) DEFAULT NULL COMMENT '分享标题',
  `shareurl` varchar(255) DEFAULT NULL COMMENT '分享链接',
  `sharedesc` varchar(255) DEFAULT NULL COMMENT '分享视频名称',
  `vdesc` varchar(255) DEFAULT NULL COMMENT '视频名称',
  `playurl` varchar(255) DEFAULT NULL COMMENT '播放地址',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面',
  `gifcover` varchar(255) DEFAULT NULL COMMENT '动态图',
  `uid` varchar(225) DEFAULT NULL COMMENT '用户id',
  `aweme_id` varchar(225) DEFAULT NULL COMMENT '视频id',
  `isdown` varchar(255) NOT NULL DEFAULT '0' COMMENT '是否下载',
  `catename` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `chnid` varchar(225) DEFAULT NULL COMMENT '挑战id',
  `chname` varchar(255) DEFAULT NULL COMMENT '挑战分类',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
