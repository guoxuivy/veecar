/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.24-log : Database - ivy
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ivy` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `ivy`;

/*Table structure for table `admin_nav` */

DROP TABLE IF EXISTS `admin_nav`;

CREATE TABLE `admin_nav` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL COMMENT '父导航id',
  `show_name` varchar(50) NOT NULL COMMENT '显示名称',
  `sys_name` varchar(100) NOT NULL COMMENT '系统路径名',
  `ord` tinyint(3) DEFAULT NULL COMMENT '显示次序',
  `type` tinyint(2) NOT NULL COMMENT '导航大类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统导航表';

/*Table structure for table `admin_user` */

DROP TABLE IF EXISTS `admin_user`;

CREATE TABLE `admin_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(40) NOT NULL COMMENT '登录账号',
  `nickname` varchar(30) DEFAULT NULL COMMENT '昵称',
  `password` varchar(100) NOT NULL COMMENT '登录密码',
  `login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统用户表';

/*Table structure for table `article` */

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章唯一id',
  `cate_id` int(11) DEFAULT NULL COMMENT '类型id',
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `summary` varchar(255) DEFAULT NULL COMMENT '摘要',
  `content` text COMMENT '文章内容',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `issue_status` tinyint(2) DEFAULT '0' COMMENT '发布状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COMMENT='文章主表';

/*Table structure for table `article_attachment` */

DROP TABLE IF EXISTS `article_attachment`;

CREATE TABLE `article_attachment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL COMMENT '所属文章',
  `name` varchar(200) NOT NULL COMMENT '附件显示名',
  `uri` varchar(200) NOT NULL COMMENT '保存路径',
  `ext` varchar(20) NOT NULL COMMENT '附件扩展名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章附件表';

/*Table structure for table `article_cate` */

DROP TABLE IF EXISTS `article_cate`;

CREATE TABLE `article_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '类型唯一id',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '夫类型id',
  `name` varchar(200) DEFAULT NULL COMMENT '类型名称',
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='文章分类表';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
