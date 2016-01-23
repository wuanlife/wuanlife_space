-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 01 月 22 日 15:46
-- 服务器版本: 5.5.38
-- PHP 版本: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `wuan`
--
CREATE DATABASE `wuan` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `wuan`;

-- --------------------------------------------------------

--
-- 表的结构 `group_base`
--

CREATE TABLE IF NOT EXISTS `group_base` (
  `ID` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '组ID',
  `name` varchar(9) CHARACTER SET gbk NOT NULL UNIQUE COMMENT '组名',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组表' AUTO_INCREMENT=4 ;



-- --------------------------------------------------------

--
-- 表的结构 `group_detail`
--

CREATE TABLE IF NOT EXISTS `group_detail` (
  `ID` int(4) unsigned NOT NULL COMMENT '组ID',
  `userID` int(5) unsigned NOT NULL COMMENT '成员ID',
  `authorization` varchar(9) CHARACTER SET utf8 NOT NULL COMMENT '身份'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组成员表';


-- --------------------------------------------------------

--
-- 表的结构 `post_base`
--

CREATE TABLE IF NOT EXISTS `post_base` (
  `ID` int(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '帖子ID',
  `userID` int(5) unsigned NOT NULL COMMENT '发帖人ID',
  `groupID` int(4) unsigned NOT NULL COMMENT '组ID',
  `title` varchar(30) CHARACTER SET gbk NOT NULL COMMENT '标题',
  `digest` int(1) NOT NULL DEFAULT '0' COMMENT '精华',
  `sticky` int(1) NOT NULL DEFAULT '0' COMMENT '置顶',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`ID`),
  KEY `userID` (`userID`,`groupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='主帖' AUTO_INCREMENT=13 ;



-- --------------------------------------------------------

--
-- 表的结构 `post_detail`
--

CREATE TABLE IF NOT EXISTS `post_detail` (
  `ID` int(5) unsigned NOT NULL COMMENT '帖子ID',
  `postID` int(5) unsigned NOT NULL COMMENT '回帖人ID',
  `replyID` int(5) unsigned DEFAULT NULL COMMENT '回复的ID',
  `text` varchar(140) COLLATE utf8_bin NOT NULL COMMENT '内容',
  `floor` int(4) NOT NULL COMMENT '楼层',
  `createTime` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '发布时间',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`ID`,`floor`),
  KEY `postID` (`postID`,`replyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='回复帖';



-- --------------------------------------------------------

--
-- 表的结构 `user_base`
--

CREATE TABLE IF NOT EXISTS `user_base` (
  `ID` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `name` varchar(20) COLLATE utf8_bin NOT NULL UNIQUE COMMENT '用户名',
  `password` varchar(35) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `nickName` varchar(20) COLLATE utf8_bin NOT NULL UNIQUE COMMENT '昵称',
  `Email` varchar(30) COLLATE utf8_bin NOT NULL UNIQUE COMMENT '邮箱',
  PRIMARY KEY (`ID`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表基本' AUTO_INCREMENT=13 ;



-- --------------------------------------------------------

--
-- 表的结构 `user_detail`
--

CREATE TABLE IF NOT EXISTS `user_detail` (
  `ID` int(5) unsigned NOT NULL COMMENT '用户ID',
  `authorization` varchar(9) CHARACTER SET utf8 NOT NULL COMMENT '身份',
  `status` int(1) NOT NULL COMMENT '状态',
  `lastLogTime` datetime NOT NULL COMMENT '上次登录',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户详情';



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
