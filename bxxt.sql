/*
Navicat MySQL Data Transfer

Source Server         : 130.211.243.6
Source Server Version : 50630
Source Host           : 130.211.243.6:3306
Source Database       : engine_bxxt

Target Server Type    : MYSQL
Target Server Version : 50630
File Encoding         : 65001

Date: 2016-05-30 01:05:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for access_log
-- ----------------------------
DROP TABLE IF EXISTS `access_log`;
CREATE TABLE `access_log` (
  `ipaddress` varchar(32) NOT NULL,
  `user` varchar(32) DEFAULT NULL,
  `location` varchar(32) DEFAULT NULL,
  `DataTime` varchar(32) NOT NULL,
  `Agent` varchar(320) DEFAULT NULL,
  PRIMARY KEY (`DataTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for api_login
-- ----------------------------
DROP TABLE IF EXISTS `api_login`;
CREATE TABLE `api_login` (
  `token` varchar(64) NOT NULL,
  `AccessKey` varchar(32) NOT NULL,
  `AccessKeyMD5` varchar(32) NOT NULL,
  `LastAddress` varchar(32) DEFAULT NULL,
  `Name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for avada
-- ----------------------------
DROP TABLE IF EXISTS `avada`;
CREATE TABLE `avada` (
  `DATATIME` varchar(32) NOT NULL,
  `NAME` varchar(32) DEFAULT NULL,
  `RGID` varchar(32) DEFAULT NULL,
  `AREA` varchar(32) DEFAULT NULL,
  `ROOM` varchar(32) DEFAULT NULL,
  `TYPE` varchar(32) DEFAULT NULL,
  `PHONE` varchar(32) DEFAULT NULL,
  `Personnel` varchar(32) DEFAULT NULL,
  `Description` varchar(3200) DEFAULT '无具体描述',
  `JDTIME` varchar(32) DEFAULT NULL,
  `DoneTIME` varchar(32) DEFAULT NULL,
  `NOW` varchar(32) NOT NULL DEFAULT 'NONE',
  `Reg_Address` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`DATATIME`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for login
-- ----------------------------
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `devname` varchar(32) NOT NULL DEFAULT '未定义',
  `NOW` varchar(32) NOT NULL DEFAULT 'WAIT',
  `TYPE` varchar(32) NOT NULL DEFAULT 'USER',
  `Phone` varchar(32) NOT NULL,
  `Email` varchar(64) NOT NULL,
  `CardNumber` varchar(32) DEFAULT NULL,
  `JIESHAO` varchar(3200) NOT NULL,
  `Last_Address` varchar(32) DEFAULT NULL COMMENT 'Last or Reg',
  `Last_Time` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
